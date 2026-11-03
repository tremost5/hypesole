<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShoeCareOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')->where('is_active', true)->where('stock', '>', 0)
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($s) => $s->where('name', 'like', '%'.$request->q.'%')->orWhere('brand', 'like', '%'.$request->q.'%')->orWhere('sku', 'like', '%'.$request->q.'%')))
            ->when(in_array($request->type, ['new', 'preloved'], true), fn ($q) => $q->where('product_type', $request->type))
            ->when($request->filled('category'), fn ($q) => $q->where('product_category_id', $request->category))
            ->latest()->paginate(12)->withQueryString();

        $gallery = ShoeCareOrder::with(['customer', 'items'])
            ->where('order_number', 'like', 'DEMO-HY-%')
            ->where('status', 'delivered')
            ->latest('id')
            ->take(5)
            ->get();

        return view('storefront', [
            'products' => $products,
            'categories' => ProductCategory::where('is_active', true)->orderBy('name')->get(),
            'gallery' => $gallery,
            'banners' => Banner::where('is_active', true)->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active && $product->stock > 0, 404);
        return view('storefront.show', compact('product'));
    }
}
