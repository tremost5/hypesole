<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        $query = Product::with('category')->latest();
        if (request('type') && in_array(request('type'), ['new', 'preloved'], true)) {
            $query->where('product_type', request('type'));
        }
        if (request('search')) {
            $term = trim((string) request('search'));
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%")->orWhere('brand', 'like', "%{$term}%"));
        }

        return view('admin.products.index', [
            'products' => $query->paginate(15)->withQueryString(),
            'categories' => ProductCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image_path'] = $request->file('image')?->store('products', 'public');
        unset($data['image']);

        DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);
            if ((int) $data['stock'] > 0) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user('web')->id,
                    'movement_type' => 'in',
                    'quantity' => $data['stock'],
                    'stock_before' => 0,
                    'stock_after' => $data['stock'],
                    'note' => 'Stok awal saat produk dibuat',
                ]);
            }
        });

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $data['slug'] = $this->uniqueSlug($data['name'], $product->id);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($data['image'], $data['stock']);

        $product->update($data);

        return back()->with('success', 'Data produk berhasil diperbarui. Perubahan stok dilakukan melalui menu stok.');
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'movement_type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['movement_type'] !== 'adjustment' && (int) $data['quantity'] < 1) {
            return back()->withErrors(['quantity' => 'Jumlah harus lebih dari nol.']);
        }

        DB::transaction(function () use ($data, $product, $request) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $before = $locked->stock;
            $quantity = (int) $data['quantity'];

            $after = match ($data['movement_type']) {
                'in' => $before + $quantity,
                'out' => $before - $quantity,
                'adjustment' => $quantity,
            };

            if ($after < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Stok keluar melebihi stok tersedia.',
                ]);
            }

            $locked->update(['stock' => $after]);

            InventoryMovement::create([
                'product_id' => $locked->id,
                'user_id' => $request->user('web')->id,
                'movement_type' => $data['movement_type'],
                'quantity' => $data['movement_type'] === 'adjustment' ? abs($after - $before) : $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok berhasil diperbarui dan tercatat di riwayat inventaris.');
    }

    public function movements(Product $product): View
    {
        return view('admin.products.movements', [
            'product' => $product,
            'movements' => $product->movements()->with('user')->latest()->paginate(20),
        ]);
    }

    public function archive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return back()->with('success', 'Produk dinonaktifkan dari katalog. Riwayat stok tetap tersimpan.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'sku' => ['required', 'string', 'max:80', \Illuminate\Validation\Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:180'],
            'brand' => ['nullable', 'string', 'max:100'],
            'product_type' => ['required', 'in:new,preloved'],
            'size' => ['nullable', 'string', 'max:30'],
            'condition_note' => ['nullable', 'string', 'max:3000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'stock' => [$product ? 'nullable' : 'required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'stock' => (int) $request->input('stock', 0),
        ];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $suffix = 2;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }
        return $slug;
    }
}
