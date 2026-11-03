<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $order->order_number }} | HYPESOLE</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<header class="bg-slate-950 px-4 py-4 text-white"><div class="mx-auto flex max-w-5xl items-center justify-between"><a href="{{ route('customer.dashboard') }}" class="font-black">HYPESOLE</a><a href="{{ route('customer.orders.index') }}" style="background:#fff;color:#0f172a" class="inline-flex min-h-10 items-center rounded-xl px-4 text-sm font-black">SEMUA ORDER</a></div></header>
<main class="mx-auto max-w-5xl px-4 py-8">
<p class="text-xs font-black tracking-widest text-cyan-800">DETAIL PERAWATAN</p><h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ $order->order_number }}</h1>
<section class="mt-5 rounded-3xl bg-gradient-to-br from-slate-950 to-cyan-900 p-5 text-white"><p class="text-sm text-cyan-100">Nomor order terdaftar atas nama</p><h2 class="mt-1 text-xl font-black">{{ $order->customer_name }}</h2><p class="mt-1 text-sm text-slate-300">{{ $order->customer_phone }}</p><p class="mt-4 text-sm text-slate-300">Diterima {{ $order->received_at?->format('d/m/Y H:i') }}</p></section>
<h2 class="mt-7 text-xl font-black">Progres setiap sepatu</h2>
<div class="mt-4 space-y-4">
@foreach($order->items as $item)
<article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
<div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-wider text-cyan-800">Sepatu {{ $loop->iteration }}</p><h3 class="mt-1 text-lg font-black">{{ trim(($item->shoe_brand ?? '').' '.($item->shoe_model ?? '')) ?: 'Sepatu' }}</h3><p class="text-sm text-slate-500">{{ $item->service_name }}</p></div><span class="rounded-full bg-slate-950 px-3 py-2 text-xs font-black text-white">{{ $item->status_label }}</span></div>
@if($item->quoted_price !== null)
<div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
    <p class="text-xs font-black uppercase tracking-wider text-slate-500">Penawaran harga final</p>
    <p class="mt-1 text-2xl font-black">Rp{{ number_format((float)$item->quoted_price,0,',','.') }}</p>
    @if($item->inspection_notes)<p class="mt-2 text-sm text-slate-600">{{ $item->inspection_notes }}</p>@endif
    @if($item->customer_approval === 'pending')
        <p class="mt-2 text-sm font-bold text-amber-800">Menunggu keputusan kamu.</p>
        <form method="POST" action="{{ route('customer.orders.items.approval', [$order, $item]) }}" class="mt-3 flex flex-wrap gap-2">
            @csrf
            <button name="decision" value="approved" style="background:#047857;color:#fff" class="min-h-11 rounded-xl px-4 py-2 text-sm font-black">SETUJUI HARGA</button>
            <button name="decision" value="rejected" style="background:#fff;color:#b91c1c;border:1px solid #fecaca" class="min-h-11 rounded-xl px-4 py-2 text-sm font-black">TOLAK PENAWARAN</button>
        </form>
    @elseif($item->customer_approval === 'approved')
        <p class="mt-2 text-sm font-bold text-emerald-700">Harga sudah kamu setujui.</p>
    @elseif($item->customer_approval === 'rejected')
        <p class="mt-2 text-sm font-bold text-red-700">Penawaran ditolak. Silakan hubungi workshop.</p>
    @endif
</div>
@endif
<dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">@foreach(['Warna'=>$item->shoe_color ?: '-','Ukuran'=>$item->shoe_size ?: '-','Treatment'=>$item->service_name,'Biaya'=>'Rp'.number_format((float)$item->service_price,0,',','.')] as $label=>$value)<div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs text-slate-500">{{ $label }}</dt><dd class="mt-1 font-bold">{{ $value }}</dd></div>@endforeach</dl>
<p class="mt-4 text-xs font-black text-slate-500">KELUHAN / KONDISI AWAL</p><p class="mt-1 whitespace-pre-line text-sm leading-6">{{ $item->issue_description }}</p>
@if($item->before_photos)<div class="mt-4"><p class="mb-2 text-xs font-black text-slate-500">FOTO KONDISI AWAL</p><div class="grid grid-cols-3 gap-2 sm:grid-cols-5">@foreach($item->before_photos as $photoIndex => $photo)<a href="{{ route('customer.media.order-photo', [$order, $item, 'before', $photoIndex]) }}" target="_blank"><img src="{{ route('customer.media.order-photo', [$order, $item, 'before', $photoIndex]) }}" alt="Foto kondisi awal sepatu" class="aspect-square w-full rounded-xl object-cover"></a>@endforeach</div></div>@endif
@if($item->after_photos)<div class="mt-4"><p class="mb-2 text-xs font-black text-slate-500">FOTO PROSES / HASIL</p><div class="grid grid-cols-3 gap-2 sm:grid-cols-5">@foreach($item->after_photos as $photoIndex => $photo)<a href="{{ route('customer.media.order-photo', [$order, $item, 'after', $photoIndex]) }}" target="_blank"><img src="{{ route('customer.media.order-photo', [$order, $item, 'after', $photoIndex]) }}" alt="Foto proses sepatu" class="aspect-square w-full rounded-xl object-cover"></a>@endforeach</div></div>@endif
</article>
@endforeach
</div>
<section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5">
<h2 class="text-xl font-black">Pickup & pengiriman</h2>
<p class="mt-1 text-sm text-slate-500">Pantau perjalanan sepatu dari alamat kamu ke workshop dan saat dikirim kembali.</p>
<div class="mt-4 space-y-3">
@forelse($order->shipments as $shipment)
<article class="rounded-2xl border border-slate-200 p-4">
<div class="flex flex-wrap justify-between gap-2"><div><p class="text-xs font-black text-teal-700">{{ $shipment->type_label }}</p><p class="mt-1 font-black">{{ $shipment->method === 'external' ? ($shipment->carrier ?: 'Ekspedisi') : ($shipment->courier?->name ?: 'Kurir internal') }}</p></div><span class="rounded-full bg-slate-900 px-3 py-2 text-xs font-black text-white">{{ $shipment->status_label }}</span></div>
<p class="mt-2 text-sm">{{ $shipment->address }}</p>
<div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-500"><span>Ongkir: Rp{{ number_format((float)$shipment->shipping_fee,0,',','.') }}</span>@if($shipment->tracking_number)<span>Resi: <strong>{{ $shipment->tracking_number }}</strong></span>@endif</div>
@if($shipment->proof_photo_path)<a href="{{ route('customer.media.shipment-proof', $shipment) }}" target="_blank" class="mt-2 inline-block text-sm font-bold text-teal-700">Lihat foto bukti</a>@endif
<div class="mt-3 space-y-2 border-l-2 border-teal-500 pl-3">@foreach($shipment->events as $event)<div><p class="text-sm font-bold">{{ ucfirst(str_replace('_',' ',$event->status)) }}</p><p class="text-xs text-slate-500">{{ $event->created_at->format('d/m/Y H:i') }} · {{ $event->description }}</p></div>@endforeach</div>
</article>
@empty<p class="text-sm text-slate-500">Belum ada jadwal pickup atau pengiriman untuk order ini.</p>@endforelse
</div></section>
<section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5"><h2 class="font-black">Riwayat order</h2><div class="mt-4 space-y-3">@forelse($order->statusLogs as $log)<div class="border-l-2 border-cyan-600 pl-4"><p class="font-bold">{{ ucfirst(str_replace('_',' ',$log->to_status)) }}</p><p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>@if($log->note)<p class="mt-1 text-sm">{{ $log->note }}</p>@endif</div>@empty<p class="text-sm text-slate-500">Belum ada riwayat.</p>@endforelse</div></section>
</main></body></html>
