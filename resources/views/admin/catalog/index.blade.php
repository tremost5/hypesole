@extends('admin.layout')
@section('title', 'Kategori Master Data')
@section('content')
    <div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">MASTER DATA</p><h1 class="mt-1 text-3xl font-black">Kategori</h1><p class="mt-2 text-sm text-slate-500">Pisahkan kategori layanan perawatan dan kategori produk sneaker.</p></div>
    <div class="mt-6 grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-black">Kategori Shoe Care</h2>
            @if(auth('web')->user()->hasPermission('catalog.manage'))
            <form method="POST" action="{{ route('admin.catalog.service-categories.store') }}" class="mt-4 space-y-2">@csrf
                <input name="name" required maxlength="100" placeholder="Contoh: Deep Cleaning" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                <textarea name="description" rows="2" placeholder="Deskripsi kategori (opsional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></textarea>
                <button type="submit" class="block w-full rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-slate-800">+ SIMPAN KATEGORI SHOE CARE</button>
            </form>
            @endif
            <div class="mt-5 space-y-3">
                @forelse($serviceCategories as $category)
                <details class="rounded-xl border border-slate-200 p-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-2"><span class="font-bold">{{ $category->name }} <small class="font-normal text-slate-500">({{ $category->services_count }} layanan)</small></span><span class="text-xs {{ $category->is_active ? 'text-emerald-700' : 'text-slate-400' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></summary>
                    @if(auth('web')->user()->hasPermission('catalog.manage'))
                    <form method="POST" action="{{ route('admin.catalog.service-categories.update', $category) }}" class="mt-3 space-y-2">@csrf @method('PUT')
                        <input name="name" value="{{ $category->name }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $category->description }}</textarea>
                        <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="1" @selected($category->is_active)>Aktif</option><option value="0" @selected(!$category->is_active)>Nonaktif</option></select>
                        <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white">SIMPAN</button>
                    </form>
                    <form method="POST" action="{{ route('admin.catalog.service-categories.destroy', $category) }}" class="mt-2" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-700">Hapus kategori</button></form>
                    @endif
                </details>
                @empty <p class="text-sm text-slate-500">Belum ada kategori layanan.</p> @endforelse
            </div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-black">Kategori Sneaker Store</h2>
            @if(auth('web')->user()->hasPermission('catalog.manage'))
            <form method="POST" action="{{ route('admin.catalog.product-categories.store') }}" class="mt-4 space-y-2">@csrf
                <input name="name" required maxlength="100" placeholder="Contoh: Running, Lifestyle, Accessories" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                <textarea name="description" rows="2" placeholder="Deskripsi kategori (opsional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></textarea>
                <button type="submit" class="block w-full rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-slate-800">+ SIMPAN KATEGORI SNEAKER STORE</button>
            </form>
            @endif
            <div class="mt-5 space-y-3">
                @forelse($productCategories as $category)
                <details class="rounded-xl border border-slate-200 p-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-2"><span class="font-bold">{{ $category->name }} <small class="font-normal text-slate-500">({{ $category->products_count }} produk)</small></span><span class="text-xs {{ $category->is_active ? 'text-emerald-700' : 'text-slate-400' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></summary>
                    @if(auth('web')->user()->hasPermission('catalog.manage'))
                    <form method="POST" action="{{ route('admin.catalog.product-categories.update', $category) }}" class="mt-3 space-y-2">@csrf @method('PUT')
                        <input name="name" value="{{ $category->name }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $category->description }}</textarea>
                        <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="1" @selected($category->is_active)>Aktif</option><option value="0" @selected(!$category->is_active)>Nonaktif</option></select>
                        <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white">SIMPAN</button>
                    </form>
                    <form method="POST" action="{{ route('admin.catalog.product-categories.destroy', $category) }}" class="mt-2" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-700">Hapus kategori</button></form>
                    @endif
                </details>
                @empty <p class="text-sm text-slate-500">Belum ada kategori produk.</p> @endforelse
            </div>
        </section>
    </div>
@endsection
