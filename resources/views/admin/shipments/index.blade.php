@extends('admin.layout')
@section('title','Kurir & Pengiriman')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-black tracking-widest text-teal-700">LOGISTIK SHOE CARE</p><h1 class="mt-1 text-3xl font-black">Kurir & Pengiriman</h1><p class="mt-2 text-sm text-slate-500">Pantau pickup, delivery, kurir internal, dan ekspedisi luar kota.</p></div><a href="{{ route('admin.shoe-care-orders.index') }}" style="background:#0f172a;color:#fff" class="inline-flex min-h-11 items-center rounded-xl px-4 font-black">LIHAT ORDER</a></div>
<div class="mt-6 space-y-3">
@forelse($shipments as $shipment)
<article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
<div class="flex flex-wrap justify-between gap-3"><div><p class="text-xs font-black text-teal-700">{{ $shipment->order->order_number }} · {{ $shipment->type_label }}</p><h2 class="mt-1 text-lg font-black">{{ $shipment->recipient_name }}</h2><p class="text-sm text-slate-500">{{ $shipment->recipient_phone }} · {{ $shipment->area }}</p></div><span class="rounded-full bg-slate-900 px-3 py-2 text-xs font-black text-white">{{ $shipment->status_label }}</span></div>
<p class="mt-3 text-sm">{{ $shipment->address }}</p>
<div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-600"><span>{{ $shipment->method === 'external' ? 'Ekspedisi: '.$shipment->carrier : 'Kurir: '.($shipment->courier?->name ?? 'Belum ditugaskan') }}</span><span>Ongkir Rp{{ number_format((float)$shipment->shipping_fee,0,',','.') }}</span><span>{{ $shipment->distance_km ? $shipment->distance_km.' km' : 'Jarak belum dicatat' }}</span>@if($shipment->tracking_number)<span>Resi: {{ $shipment->tracking_number }}</span>@endif</div>
@if($shipment->method==='internal' && !$shipment->courier_id && !in_array($shipment->status,['delivered','cancelled']))
<form method="POST" action="{{ route('admin.shipments.assign',$shipment) }}" class="mt-4 flex flex-wrap gap-2">@csrf @method('PATCH')<select name="courier_id" required class="min-h-10 rounded-xl border border-slate-300 px-3"><option value="">Pilih kurir</option>@foreach(\App\Models\User::where('is_active',true)->whereHas('role',fn($q)=>$q->where('slug','courier'))->orderBy('name')->get() as $courier)<option value="{{ $courier->id }}">{{ $courier->name }}</option>@endforeach</select><button style="background:#0f766e;color:#fff" class="min-h-10 rounded-xl px-4 text-sm font-black">TUGASKAN KURIR</button></form>
@endif
@if($shipment->method==='external' && $shipment->tracking_number)<p class="mt-3 text-sm font-bold text-blue-700">Nomor resi tercatat. Pelanggan dapat melihatnya pada detail order.</p>@endif
@if(!in_array($shipment->status,['delivered','cancelled']))
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.shipments.status',$shipment) }}" class="mt-4 grid gap-2 rounded-xl bg-slate-50 p-3 sm:grid-cols-4">@csrf @method('PATCH')
<select name="status" class="rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm">@foreach(['waiting_assignment'=>'Menunggu kurir','assigned'=>'Kurir ditugaskan','picked_up'=>'Sudah dijemput','in_transit'=>'Dalam perjalanan','delivered'=>'Terkirim','failed'=>'Gagal','cancelled'=>'Dibatalkan'] as $key=>$label)<option value="{{ $key }}" @selected($shipment->status===$key)>{{ $label }}</option>@endforeach</select>
<input name="tracking_number" value="{{ $shipment->tracking_number }}" placeholder="Nomor resi (jika ekspedisi)" class="rounded-lg border border-slate-300 px-2 py-2 text-sm">
<input type="file" name="proof_photo" accept="image/jpeg,image/png,image/webp" class="rounded-lg border border-slate-300 bg-white p-1 text-xs">
<button style="background:#0f172a;color:#fff" class="rounded-lg px-3 py-2 text-xs font-black">UPDATE STATUS</button>
<textarea name="notes" placeholder="Catatan update" class="rounded-lg border border-slate-300 px-2 py-2 text-sm sm:col-span-4"></textarea>
</form>
@endif
</article>
@empty<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-black">Belum ada pengiriman</p><p class="mt-1 text-sm text-slate-500">Buat pickup atau delivery dari detail order Shoe Care.</p></div>@endforelse
</div><div class="mt-5">{{ $shipments->links() }}</div>
@endsection
