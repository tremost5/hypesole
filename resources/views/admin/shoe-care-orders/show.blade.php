@extends('admin.layout')
@section('title', 'Detail Order '.$order->order_number)
@section('content')
<div class="flex flex-wrap items-start justify-between gap-3">
    <div><p class="text-xs font-black uppercase tracking-[.2em] text-cyan-700">Detail transaksi</p><h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ $order->order_number }}</h1><p class="mt-2 text-sm text-slate-500">{{ $order->customer_name }} · {{ $order->customer_phone }}</p></div>
    <a href="{{ route('admin.shipments.create', $order) }}" style="background:#0f766e;color:#fff" class="inline-flex min-h-11 items-center rounded-xl px-4 font-black">+ ATUR PICKUP / DELIVERY</a>
    <a href="{{ route('admin.shoe-care-orders.index') }}" class="inline-flex min-h-11 items-center rounded-xl border border-slate-300 bg-white px-4 font-bold text-slate-700">← Kembali</a>
</div>

<section class="mt-5 grid gap-3 sm:grid-cols-3">
    <div class="rounded-2xl bg-slate-950 p-4 text-white"><p class="text-xs text-slate-300">Status order</p><p class="mt-1 text-lg font-black">{{ $order->status_label }}</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Jumlah sepatu</p><p class="mt-1 text-lg font-black">{{ $order->items->count() }} pasang</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Total estimasi treatment</p><p class="mt-1 text-lg font-black">Rp{{ number_format((float)$order->items->sum('service_price'),0,',','.') }}</p></div>
</section>

<div class="mt-6 flex items-center justify-between gap-3"><div><h2 class="text-xl font-black">Item sepatu & progres</h2><p class="text-sm text-slate-500">Status setiap pasang dapat diperbarui secara terpisah.</p></div></div>
<div class="mt-4 space-y-4">
@forelse($order->items as $item)
    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4">
            <div><p class="text-xs font-black uppercase tracking-wider text-cyan-800">Sepatu {{ $loop->iteration }}</p><h3 class="mt-1 text-lg font-black">{{ trim(($item->shoe_brand ?? '').' '.($item->shoe_model ?? '')) ?: 'Sepatu tanpa identitas' }}</h3><p class="text-sm text-slate-500">{{ $item->service_name }} · Rp{{ number_format((float)$item->service_price,0,',','.') }}</p></div>
            <span class="rounded-full bg-slate-950 px-3 py-2 text-xs font-black text-white">{{ $item->status_label }}</span>
        </div>
        <div class="grid gap-5 p-5 lg:grid-cols-[1.2fr_.8fr]">
            <div>
                <dl class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                    @foreach(['Warna'=>$item->shoe_color ?: '-','Ukuran'=>$item->shoe_size ?: '-','Staff'=>$item->assignedStaff?->name ?: 'Belum ditugaskan'] as $label=>$value)
                    <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-500">{{ $label }}</dt><dd class="mt-1 font-bold">{{ $value }}</dd></div>
                    @endforeach
                </dl>
                <p class="mt-4 text-xs font-black uppercase text-slate-500">Keluhan / kondisi awal</p><p class="mt-1 whitespace-pre-line text-sm leading-6">{{ $item->issue_description }}</p>
                @if($item->internal_notes)<p class="mt-3 text-xs font-black uppercase text-slate-500">Catatan internal</p><p class="mt-1 whitespace-pre-line text-sm">{{ $item->internal_notes }}</p>@endif
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div><p class="mb-2 text-xs font-black text-slate-500">FOTO AWAL</p><div class="grid grid-cols-3 gap-2">@forelse($item->before_photos ?? [] as $photoIndex => $photo)<a href="{{ route('admin.media.order-photo', [$order, $item, 'before', $photoIndex]) }}" target="_blank"><img src="{{ route('admin.media.order-photo', [$order, $item, 'before', $photoIndex]) }}" alt="Foto awal" class="aspect-square w-full rounded-lg object-cover"></a>@empty<span class="text-xs text-slate-400">Belum ada foto</span>@endforelse</div></div>
                    <div><p class="mb-2 text-xs font-black text-slate-500">FOTO HASIL</p><div class="grid grid-cols-3 gap-2">@forelse($item->after_photos ?? [] as $photoIndex => $photo)<a href="{{ route('admin.media.order-photo', [$order, $item, 'after', $photoIndex]) }}" target="_blank"><img src="{{ route('admin.media.order-photo', [$order, $item, 'after', $photoIndex]) }}" alt="Foto hasil" class="aspect-square w-full rounded-lg object-cover"></a>@empty<span class="text-xs text-slate-400">Belum ada foto</span>@endforelse</div></div>
                </div>
            </div>
            <div class="mb-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 lg:col-span-2">
                <h4 class="font-black text-amber-950">Inspeksi & penawaran harga</h4>
                <p class="mt-1 text-xs text-amber-900">Masukkan harga final untuk sepatu ini. Pelanggan harus menyetujui sebelum pengerjaan dimulai.</p>
                @if($item->customer_approval === 'pending')
                    <p class="mt-2 text-sm font-bold text-amber-900">Menunggu persetujuan pelanggan · Rp{{ number_format((float)$item->quoted_price,0,',','.') }}</p>
                @elseif($item->customer_approval === 'approved')
                    <p class="mt-2 text-sm font-bold text-emerald-800">Harga disetujui · Rp{{ number_format((float)$item->quoted_price,0,',','.') }}</p>
                @elseif($item->customer_approval === 'rejected')
                    <p class="mt-2 text-sm font-bold text-red-700">Penawaran ditolak pelanggan.</p>
                @endif
                <form method="POST" action="{{ route('admin.shoe-care-orders.items.quote', [$order, $item]) }}" class="mt-3 grid gap-3 sm:grid-cols-[1fr_2fr_auto]">
                    @csrf @method('PATCH')
                    <label class="block text-xs font-bold text-slate-700">Harga final (Rp)
                        <input type="number" name="quoted_price" min="0" step="1000" required value="{{ old('quoted_price', $item->quoted_price ?? $item->service_price) }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm">
                    </label>
                    <label class="block text-xs font-bold text-slate-700">Catatan inspeksi
                        <input type="text" name="inspection_notes" value="{{ old('inspection_notes', $item->inspection_notes) }}" placeholder="Contoh: noda membandel, perlu deep clean" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm">
                    </label>
                    <button type="submit" style="background:#92400e;color:#fff" class="min-h-11 self-end rounded-xl px-4 py-3 text-sm font-black">KIRIM PENAWARAN</button>
                </form>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.shoe-care-orders.items.status', [$order, $item]) }}" class="h-fit space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                @csrf @method('PATCH')
                <h4 class="font-black">Update progres sepatu ini</h4>
                <label class="block"><span class="mb-1.5 block text-xs font-bold">Status pengerjaan</span><select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3">@foreach($statuses as $key=>$label)<option value="{{ $key }}" @selected($item->status===$key)>{{ $label }}</option>@endforeach</select></label>
                <label class="block"><span class="mb-1.5 block text-xs font-bold">Staff penanggung jawab</span><select name="assigned_staff_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3"><option value="">Belum ditentukan</option>@foreach($staff as $person)<option value="{{ $person->id }}" @selected($item->assigned_staff_id===$person->id)>{{ $person->name }}</option>@endforeach</select></label>
                <label class="block"><span class="mb-1.5 block text-xs font-bold">Catatan internal</span><textarea name="internal_notes" rows="2" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2">{{ $item->internal_notes }}</textarea></label>
                <label class="block"><span class="mb-1.5 block text-xs font-bold">Foto proses / hasil (maks. 5 foto)</span><input type="file" name="after_photos[]" accept="image/jpeg,image/png,image/webp" multiple class="block w-full rounded-xl border border-slate-300 bg-white p-2 text-xs"></label>
                <button type="submit" style="background:#0f172a;color:#fff" class="min-h-11 w-full rounded-xl px-4 py-2.5 text-sm font-black">SIMPAN PROGRES ITEM</button>
            </form>
        </div>
    </article>
@empty
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada item sepatu pada order ini.</div>
@endforelse
</div>

<section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5">
    <h2 class="text-lg font-black">Riwayat aktivitas order</h2>
    <div class="mt-4 space-y-3">
        @forelse($order->statusLogs as $log)
            <div class="border-l-2 border-cyan-600 pl-4"><p class="font-bold">{{ $statuses[$log->to_status] ?? $log->to_status }}</p><p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }} · {{ $log->user?->name ?? 'Sistem' }}</p>@if($log->note)<p class="mt-1 text-sm">{{ $log->note }}</p>@endif</div>
        @empty<p class="text-sm text-slate-500">Belum ada riwayat.</p>@endforelse
    </div>
</section>
@endsection
