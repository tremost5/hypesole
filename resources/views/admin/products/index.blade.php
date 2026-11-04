@extends('admin.layout')
@section('title', 'Produk Sneaker Store')
@section('content')
    <div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">SNEAKER STORE</p><h1 class="mt-1 text-3xl font-black">Produk & Inventaris</h1><p class="mt-2 text-sm text-slate-500">Kelola sneaker baru, preloved, harga, foto, dan stok toko.</p></div>
    @if(auth('web')->user()->hasPermission('products.manage'))
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-lg font-black">Tambah produk</h2>
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2">@csrf
            <input name="sku" required maxlength="80" placeholder="SKU unik" class="rounded-xl border border-slate-300 px-3 py-3">
            <input name="name" required maxlength="180" placeholder="Nama produk" class="rounded-xl border border-slate-300 px-3 py-3">
            <input name="brand" maxlength="100" placeholder="Brand" class="rounded-xl border border-slate-300 px-3 py-3">
            <select name="product_category_id" class="rounded-xl border border-slate-300 px-3 py-3"><option value="">Tanpa kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
            <select name="product_type" required class="rounded-xl border border-slate-300 px-3 py-3"><option value="new">Baru</option><option value="preloved">Preloved</option></select>
            <input name="size" maxlength="30" placeholder="Ukuran (contoh: 42 / US 9)" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="number" name="price" required min="0" step="1000" placeholder="Harga jual (Rp)" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="number" name="cost_price" min="0" step="1000" placeholder="Harga modal (internal, opsional)" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="number" name="stock" required min="0" placeholder="Stok awal" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
            <textarea name="condition_note" rows="2" placeholder="Kondisi barang (terutama preloved)" class="rounded-xl border border-slate-300 px-3 py-3 sm:col-span-2"></textarea>
            <textarea name="description" rows="2" placeholder="Deskripsi produk" class="rounded-xl border border-slate-300 px-3 py-3 sm:col-span-2"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Aktif di katalog</label>
            <button class="rounded-xl bg-slate-950 px-4 py-3 font-bold text-white sm:col-span-2">Simpan produk</button>
        </form>
    </section>
    @endif
    <form method="GET" class="mt-6 grid gap-2 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, atau brand..." class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
        <select name="type" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm"><option value="">Semua jenis</option><option value="new" @selected(request('type')==='new')>Baru</option><option value="preloved" @selected(request('type')==='preloved')>Preloved</option></select>
        <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white">Filter</button>
    </form>
    <section class="mt-5 grid gap-4 md:grid-cols-2">
        @forelse($products as $product)
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="grid grid-cols-[112px_1fr] gap-4 p-4">
                <div class="h-32 overflow-hidden rounded-xl bg-slate-100">@if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">@else<div class="grid h-full place-items-center text-xs text-slate-400">No image</div>@endif</div>
                <div class="min-w-0"><div class="flex flex-wrap gap-2"><span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase">{{ $product->product_type === 'new' ? 'Baru' : 'Preloved' }}</span><span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $product->is_active ? 'Aktif' : 'Arsip' }}</span></div>
                    <h2 class="mt-2 break-words font-black">{{ $product->name }}</h2><p class="text-xs text-slate-500">{{ $product->brand }} · SKU {{ $product->sku }}</p>
                    <p class="mt-2 text-sm font-bold">Rp {{ number_format((float)$product->price, 0, ',', '.') }}</p><p class="mt-1 text-xs {{ $product->stock > 0 ? 'text-emerald-700' : 'text-rose-700' }}">Stok: {{ $product->stock }} {{ $product->size ? '· Size '.$product->size : '' }}</p>
                </div>
            </div>
            <div class="border-t border-slate-100 p-4">
                <div class="flex flex-wrap gap-3 text-xs font-bold"><a href="{{ route('admin.products.movements', $product) }}" class="text-cyan-800">Riwayat stok</a>
                    @if(auth('web')->user()->hasPermission('inventory.manage'))
                    <button type="button" onclick="document.getElementById('stock-{{ $product->id }}').classList.toggle('hidden')" class="text-cyan-800">Update stok</button>
                    @endif
                    @if(auth('web')->user()->hasPermission('products.manage'))
                    <button type="button" onclick="document.getElementById('edit-{{ $product->id }}').classList.toggle('hidden')" class="text-slate-700">Edit produk</button>
                    @endif
                </div>
                @if(auth('web')->user()->hasPermission('inventory.manage'))
                <form id="stock-{{ $product->id }}" method="POST" action="{{ route('admin.products.stock', $product) }}" class="mt-3 hidden grid gap-2 rounded-xl bg-slate-50 p-3 sm:grid-cols-3">@csrf @method('PATCH')
                    <select name="movement_type" class="rounded-lg border border-slate-300 px-2 py-2 text-sm"><option value="in">Stok masuk</option><option value="out">Stok keluar</option><option value="adjustment">Set stok akhir</option></select>
                    <input type="number" name="quantity" min="0" required placeholder="Jumlah / stok akhir" class="rounded-lg border border-slate-300 px-2 py-2 text-sm">
                    <input name="note" maxlength="255" placeholder="Catatan" class="rounded-lg border border-slate-300 px-2 py-2 text-sm sm:col-span-2">
                    <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white">Simpan stok</button>
                </form>
                @endif
                @if(auth('web')->user()->hasPermission('products.manage'))
                <details id="edit-{{ $product->id }}" class="mt-3 hidden">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="grid gap-2 sm:grid-cols-2">@csrf @method('PUT')
                        <input name="sku" value="{{ $product->sku }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <input name="name" value="{{ $product->name }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <input name="brand" value="{{ $product->brand }}" placeholder="Brand" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <select name="product_category_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Tanpa kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($product->product_category_id === $category->id)>{{ $category->name }}</option>@endforeach</select>
                        <select name="product_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="new" @selected($product->product_type==='new')>Baru</option><option value="preloved" @selected($product->product_type==='preloved')>Preloved</option></select>
                        <input name="size" value="{{ $product->size }}" placeholder="Ukuran" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <input type="number" name="price" value="{{ (float)$product->price }}" min="0" step="1000" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <input type="number" name="cost_price" value="{{ $product->cost_price }}" min="0" step="1000" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <textarea name="condition_note" rows="2" placeholder="Kondisi" class="rounded-lg border border-slate-300 px-3 py-2 text-sm sm:col-span-2">{{ $product->condition_note }}</textarea>
                        <textarea name="description" rows="2" placeholder="Deskripsi" class="rounded-lg border border-slate-300 px-3 py-2 text-sm sm:col-span-2">{{ $product->description }}</textarea>
                        <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            @if($product->image_path)
                                <div class="mb-2 flex items-center gap-3">
                                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="Foto {{ $product->name }} saat ini" class="h-16 w-16 rounded-lg border border-slate-200 bg-white object-contain">
                                    <span class="text-xs text-slate-600">Foto saat ini. Pilih file baru untuk menggantinya; kosongkan jika tidak ingin mengubah foto.</span>
                                </div>
                            @else
                                <p class="mb-2 text-xs text-slate-500">Belum ada foto produk.</p>
                            @endif
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs">
                            <p class="mt-1 text-[11px] text-slate-500">Format JPG, PNG, atau WebP. Maksimal 3 MB.</p>
                        </div>
                        <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="1" @selected($product->is_active)>Aktif</option><option value="0" @selected(!$product->is_active)>Nonaktif</option></select>
                        <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white sm:col-span-2">Simpan perubahan</button>
                    </form>
                    @if($product->is_active)
                    <form method="POST" action="{{ route('admin.products.archive', $product) }}" class="mt-2" onsubmit="return confirm('Arsipkan produk ini?')">@csrf @method('PATCH')<button class="text-xs font-bold text-rose-700">Arsipkan produk</button></form>
                    @endif
                </details>
                @endif
            </div>
        </article>
        @empty <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 md:col-span-2">Produk belum tersedia.</div> @endforelse
    </section>
    <div class="mt-5">{{ $products->links() }}</div>
@endsection
