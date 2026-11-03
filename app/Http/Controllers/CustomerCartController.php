<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class CustomerCartController extends Controller {
    public function index(Request $request): View {
        $cart=$request->session()->get('store_cart',[]);
        $products=Product::whereIn('id',array_keys($cart))->where('is_active',true)->get()->keyBy('id');
        $lines=[]; $subtotal=0;
        foreach($cart as $id=>$qty) {
            $product=$products->get($id);
            if(!$product) continue;
            $qty=min((int)$qty,(int)$product->stock);
            if($qty<1) continue;
            $line=(float)$product->price*$qty; $subtotal+=$line;
            $lines[]=['product'=>$product,'quantity'=>$qty,'line_total'=>$line];
        }
        return view('customer.store.cart',compact('lines','subtotal'));
    }
    public function add(Request $request, Product $product): RedirectResponse {
        abort_unless($product->is_active && $product->stock>0,404);
        $data=$request->validate(['quantity'=>['required','integer','min:1','max:20']]);
        $cart=$request->session()->get('store_cart',[]);
        $next=(int)($cart[$product->id]??0)+(int)$data['quantity'];
        if($next>$product->stock) return back()->withErrors(['quantity'=>'Jumlah melebihi stok tersedia.']);
        $cart[$product->id]=$next;
        $request->session()->put('store_cart',$cart);
        return redirect()->route('customer.cart.index')->with('success','Produk berhasil dimasukkan ke keranjang.');
    }
    public function update(Request $request, Product $product): RedirectResponse {
        $data=$request->validate(['quantity'=>['required','integer','min:0','max:20']]);
        $cart=$request->session()->get('store_cart',[]);
        $qty=(int)$data['quantity'];
        if($qty===0) unset($cart[$product->id]);
        else {
            if($qty>$product->stock) return back()->withErrors(['quantity'=>'Jumlah melebihi stok tersedia.']);
            $cart[$product->id]=$qty;
        }
        $request->session()->put('store_cart',$cart);
        return back()->with('success','Keranjang berhasil diperbarui.');
    }
    public function remove(Request $request, Product $product): RedirectResponse {
        $cart=$request->session()->get('store_cart',[]);
        unset($cart[$product->id]);
        $request->session()->put('store_cart',$cart);
        return back()->with('success','Produk dihapus dari keranjang.');
    }
}
