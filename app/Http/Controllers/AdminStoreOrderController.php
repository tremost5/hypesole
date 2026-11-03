<?php
namespace App\Http\Controllers;
use App\Models\InventoryMovement;
use App\Models\StoreOrder;
use App\Models\StoreOrderStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class AdminStoreOrderController extends Controller {
    private const STATUSES=['pending_confirmation'=>'Menunggu konfirmasi','confirmed'=>'Dikonfirmasi','processing'=>'Diproses','ready'=>'Siap diserahkan','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
    public function index(Request $request): View {
        $orders=StoreOrder::with('customer')->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))
            ->when($request->filled('q'),fn($q)=>$q->where(fn($s)=>$s->where('order_number','like','%'.$request->q.'%')->orWhere('recipient_name','like','%'.$request->q.'%')->orWhere('recipient_phone','like','%'.$request->q.'%')))
            ->latest()->paginate(20)->withQueryString();
        return view('admin.store-orders.index',['orders'=>$orders,'statuses'=>self::STATUSES]);
    }
    public function show(StoreOrder $order): View {
        $order->load(['items','customer','statusLogs.user']);
        return view('admin.store-orders.show',['order'=>$order,'statuses'=>self::STATUSES]);
    }
    public function update(Request $request,StoreOrder $order): RedirectResponse {
        $data=$request->validate([
            'status'=>['required','in:pending_confirmation,confirmed,processing,ready,completed,cancelled'],
            'payment_status'=>['required','in:unpaid,paid,refunded'],
            'note'=>['nullable','string','max:1000'],
        ]);
        DB::transaction(function() use($order,$data,$request) {
            $locked=StoreOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $old=$locked->status; $next=$data['status'];
            if($old==='cancelled' && $next!=='cancelled') throw \Illuminate\Validation\ValidationException::withMessages(['status'=>'Pesanan yang dibatalkan tidak dapat diaktifkan kembali.']);
            if($old==='pending_confirmation' && $next!=='pending_confirmation' && $next!=='cancelled' && !$locked->stock_deducted_at) {
                foreach($locked->items as $item) {
                    if(!$item->product_id) throw \Illuminate\Validation\ValidationException::withMessages(['status'=>'Produk '.$item->product_name.' sudah tidak tersedia.']);
                    $product=\App\Models\Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if(!$product || !$product->is_active || $product->stock < $item->quantity) throw \Illuminate\Validation\ValidationException::withMessages(['status'=>'Stok '.$item->product_name.' tidak mencukupi untuk konfirmasi.']);
                    $before=$product->stock; $after=$before-$item->quantity;
                    $product->update(['stock'=>$after]);
                    InventoryMovement::create(['product_id'=>$product->id,'user_id'=>$request->user('web')->id,'movement_type'=>'out','quantity'=>$item->quantity,'stock_before'=>$before,'stock_after'=>$after,'note'=>'Penjualan order '.$locked->order_number]);
                }
                $locked->stock_deducted_at=now();
            }
            if($old!=='cancelled' && $next==='cancelled') {
                if($locked->stock_deducted_at) foreach($locked->items as $item) {
                    if(!$item->product_id) continue;
                    $product=\App\Models\Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if(!$product) continue;
                    $before=$product->stock; $after=$before+$item->quantity;
                    $product->update(['stock'=>$after]);
                    InventoryMovement::create(['product_id'=>$product->id,'user_id'=>$request->user('web')->id,'movement_type'=>'in','quantity'=>$item->quantity,'stock_before'=>$before,'stock_after'=>$after,'note'=>'Restock pembatalan order '.$locked->order_number]);
                }
                $locked->cancelled_at=now();
            }
            if($old==='pending_confirmation' && $next!=='pending_confirmation') $locked->confirmed_at=now();
            if($next==='completed') $locked->completed_at=now();
            $locked->status=$next; $locked->payment_status=$data['payment_status']; $locked->save();
            if($old!==$next) StoreOrderStatusLog::create(['store_order_id'=>$locked->id,'user_id'=>$request->user('web')->id,'from_status'=>$old,'to_status'=>$next,'note'=>$data['note']??null]);
        });
        return back()->with('success','Status pesanan berhasil diperbarui.');
    }
}
