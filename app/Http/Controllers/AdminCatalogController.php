<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCatalogController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog.index', [
            'serviceCategories' => ServiceCategory::withCount('services')->orderBy('name')->get(),
            'productCategories' => ProductCategory::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function storeServiceCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        ServiceCategory::create([
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(ServiceCategory::class, $data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori layanan berhasil ditambahkan.');
    }

    public function updateServiceCategory(Request $request, ServiceCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update([
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(ServiceCategory::class, $data['name'], $category->id),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        return back()->with('success', 'Kategori layanan berhasil diperbarui.');
    }

    public function destroyServiceCategory(ServiceCategory $category): RedirectResponse
    {
        if ($category->services()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih digunakan layanan. Pindahkan layanan terlebih dahulu.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori layanan berhasil dihapus.');
    }

    public function storeProductCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        ProductCategory::create([
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(ProductCategory::class, $data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function updateProductCategory(Request $request, ProductCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update([
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(ProductCategory::class, $data['name'], $category->id),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        return back()->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroyProductCategory(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih digunakan produk. Pindahkan produk terlebih dahulu.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori produk berhasil dihapus.');
    }

    private function uniqueSlug(string $model, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $suffix = 2;

        while ($model::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
