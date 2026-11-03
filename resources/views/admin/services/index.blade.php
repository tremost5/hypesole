@extends('admin.layout')
@section('title', 'Layanan Shoe Care')
@section('content')
    <div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">SHOE CARE</p><h1 class="mt-1 text-3xl font-black">Layanan & Treatment</h1><p class="mt-2 text-sm text-slate-500">Atur jenis perawatan, estimasi durasi, harga, dan foto layanan.</p></div>
    @if(auth('web')->user()->hasPermission('services.manage'))
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-lg font-black">Tambah layanan</h2>
        @if($categories->isEmpty())<p class="mt-3 text-sm text-amber-700">Buat kategori Shoe Care terlebih dahulu di menu Kategori.</p>@else
        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2">@csrf
            <input name="name" required maxlength="150" placeholder="Nama layanan, misalnya Basic Cleaning" class="rounded-xl border border-slate-300 px-3 py-3">
            <select name="service_category_id" required class="rounded-xl border border-slate-300 px-3 py-3">@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
            <input type="number" name="price" required min="0" step="1000" placeholder="Harga (Rp)" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="number" name="duration_minutes" min="1" placeholder="Durasi (menit, opsional)" class="rounded-xl border border-slate-300 px-3 py-3">
            <textarea name="description" rows="3" placeholder="Deskripsi layanan" class="rounded-xl border border-slate-300 px-3 py-3 sm:col-span-2"></textarea>
            <label class="text-sm">Foto layanan <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm"></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Tampilkan sebagai layanan aktif</label>
            <button class="rounded-xl bg-slate-950 px-4 py-3 font-bold text-white sm:col-span-2">Simpan layanan</button>
        </form>
        @endif
    </section>
    @endif
    <section class="mt-6 grid gap-4 md:grid-cols-2">
        @forelse($services as $service)
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            @if($service->image_path)<img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" class="h-44 w-full object-cover">@endif
            <div class="p-5">
                <div class="flex items-start justify-between gap-3"><div><span class="text-xs font-bold uppercase tracking-wider text-cyan-800">{{ $service->category?->name }}</span><h2 class="mt-1 text-lg font-black">{{ $service->name }}</h2></div><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $service->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                <p class="mt-2 text-sm text-slate-500">{{ $service->description }}</p>
                <div class="mt-3 flex justify-between text-sm"><strong>Rp {{ number_format((float)$service->price, 0, ',', '.') }}</strong><span class="text-slate-500">{{ $service->duration_minutes ? $service->duration_minutes.' menit' : 'Durasi fleksibel' }}</span></div>
                @if(auth('web')->user()->hasPermission('services.manage'))
                <details class="mt-4 border-t border-slate-100 pt-3"><summary class="cursor-pointer text-sm font-bold text-cyan-800">Edit layanan</summary>
                    <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" class="mt-3 grid gap-2">@csrf @method('PUT')
                        <input name="name" value="{{ $service->name }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <select name="service_category_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">@foreach($categories as $category)<option value="{{ $category->id }}" @selected($service->service_category_id === $category->id)>{{ $category->name }}</option>@endforeach</select>
                        <input type="number" name="price" value="{{ (float)$service->price }}" min="0" step="1000" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <input type="number" name="duration_minutes" value="{{ $service->duration_minutes }}" min="1" placeholder="Durasi menit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <textarea name="description" rows="2" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $service->description }}</textarea>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="text-sm">
                        <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="1" @selected($service->is_active)>Aktif</option><option value="0" @selected(!$service->is_active)>Nonaktif</option></select>
                        <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white">Simpan perubahan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-3" onsubmit="return confirm('Hapus layanan ini?')">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">Hapus layanan</button></form>
                </details>
                @endif
            </div>
        </article>
        @empty <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 md:col-span-2">Belum ada layanan Shoe Care.</div> @endforelse
    </section>
@endsection
