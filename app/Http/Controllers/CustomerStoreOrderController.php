<?php
namespace App\Http\Controllers;
use App\Models\CustomerAddress;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\StoreOrderStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
class CustomerStoreOrderController extends Controller {
    public function checkout(Request $request): View|RedirectResponse {
        $cart=$request->session()->get('store_cart',[]);
        if(!$cart) return redirect()->route('customer.cart.index')->withErrors(['cart'=>'Keranjang masih kosong.']);
        $products=Product::whereIn('id',array_keys($cart))->where('is_active',true)->get();
        if($products->count()!==count($cart)) return redirect()->route('customer.cart.index')->withErrors(['cart'=>'Ada produk yang sudah tidak tersedia. Periksa keranjang.']);
        $subtotal=0;
        foreach($products as $p) {
            $qty=(int)$cart[$p->id];
            if($qty<1 || $qty>$p->stock) return redirect()->route('customer.cart.index')->withErrors(['cart'=>'Stok '.$p->name.' tidak mencukupi.']);
            $subtotal+=(float)$p->price*$qty;
        }
        $addresses=$request->user('customer')->addresses()->orderByDesc('is_default')->latest()->get();
        if($addresses->isEmpty()) return redirect()->route('customer.dashboard')->withErrors(['address'=>'Tambahkan alamat pengiriman di profil customer sebelum checkout.']);
        return view('customer.store.checkout',compact('products','subtotal','addresses'));
    }
    public function place(Request $request): RedirectResponse {
        $customer=$request->user('customer');
        $data=$request->validate([
            'customer_address_id'=>['required','integer'],
            'payment_method'=>['required','in:cash,transfer'],
            'customer_notes'=>['nullable','string','max:2000'],
        ]);
        $address=$customer->addresses()->findOrFail($data['customer_address_id']);
        $cart=$request->session()->get('store_cart',[]);
        if(!$cart) return redirect()->route('customer.cart.index')->withErrors(['cart'=>'Keranjang masih kosong.']);

        $order=DB::transaction(function() use($cart,$customer,$address,$data,$request) {
            $ids=array_map('intval',array_keys($cart));
            $products=Product::whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if($products->count()!==count($cart)) throw \Illuminate\Validation\ValidationException::withMessages(['cart'=>'Ada produk yang tidak tersedia.']);
            $subtotal=0;
            foreach($cart as $id=>$qty) {
                $p=$products->get((int)$id); $qty=(int)$qty;
                if(!$p || !$p->is_active || $qty<1 || $qty>$p->stock) throw \Illuminate\Validation\ValidationException::withMessages(['cart'=>'Stok produk berubah. Periksa kembali keranjang.']);
                $subtotal+=(float)$p->price*$qty;
            }
            $shipping=0; // Shipping cost is not customer-editable; configure it in a later shipping settings phase.
            do { $number='SS-'.now()->format('ymd').'-'.Str::upper(Str::random(5)); }
            while(StoreOrder::where('order_number',$number)->exists());

            $order=StoreOrder::create([
                'order_number'=>$number,'customer_id'=>$customer->id,'customer_address_id'=>$address->id,
                'recipient_name'=>$address->recipient_name,'recipient_phone'=>$address->phone,
                'shipping_address'=>$address->address_line,'shipping_notes'=>$address->notes,
                'subtotal'=>$subtotal,'shipping_cost'=>$shipping,'total'=>$subtotal+$shipping,
                'payment_method'=>$data['payment_method'],'payment_status'=>'unpaid',
                'status'=>'pending_confirmation','customer_notes'=>$data['customer_notes']??null,
            ]);
            foreach($cart as $id=>$qty) {
                $p=$products->get((int)$id); $qty=(int)$qty;
                StoreOrderItem::create([
                    'store_order_id'=>$order->id,'product_id'=>$p->id,'product_name'=>$p->name,
                    'sku'=>$p->sku,'product_type'=>$p->product_type,'size'=>$p->size,
                    'unit_price'=>$p->price,'quantity'=>$qty,'line_total'=>(float)$p->price*$qty,
                ]);
            }
            StoreOrderStatusLog::create(['store_order_id'=>$order->id,'from_status'=>null,'to_status'=>'pending_confirmation','note'=>'Pesanan dibuat customer.']);
            return $order;
        });
        $request->session()->forget('store_cart');
        return redirect()->route('customer.store-orders.show',$order)->with('success','Pesanan berhasil dibuat. Menunggu konfirmasi toko.');
    }
    public function index(Request $request): View {
        $orders=StoreOrder::where('customer_id',$request->user('customer')->id)->withCount('items')->latest()->paginate(10);
        return view('customer.store.orders.index',compact('orders'));
    }
    public function show(Request $request, StoreOrder $order): View {
        abort_unless((int)$order->customer_id===(int)$request->user('customer')->id,404);
        $order->load(['items','statusLogs']);
        return view('customer.store.orders.show',compact('order'));
    }
}
