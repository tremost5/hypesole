@extends('admin.layout')
@section('title','Dashboard Kurir')
@section('content')
<div><p class="text-xs font-black tracking-widest text-teal-700">PANEL PETUGAS PENGIRIMAN</p><h1 class="mt-1 text-3xl font-black">Tugas Kurir</h1><p class="mt-2 text-sm text-slate-500">Update status pickup dan delivery. Unggah foto bukti saat serah terima.</p></div>
<div class="mt-6 space-y-4">
@forelse($shipments as $shipment)
<article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
<div class="flex flex-wrap justify-between gap-3"><div><p class="text-xs font-black text-teal-700">{{ $shipment->order->order_number }} · {{ $shipment->type_label }}</p><h2 class="mt-1 text-xl font-black">{{ $shipment->recipient_name }}</h2><p class="text-sm text-slate-500">{{ $shipment->recipient_phone }}</p></div><span class="rounded-full bg-slate-900 px-3 py-2 text-xs font-black text-white">{{ $shipment->status_label }}</span></div>
<p class="mt-3 text-sm leading-6">{{ $shipment->address }}</p><p class="mt-2 text-xs text-slate-500">{{ $shipment->area }}</p>
@if($shipment->proof_photo_path)<a class="mt-3 inline-block text-sm font-bold text-teal-700" href="{{ route('admin.media.shipment-proof', $shipment) }}" target="_blank">Lihat bukti foto terakhir</a>@endif
@if(!in_array($shipment->status,['delivered','cancelled']))
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.courier.shipments.status',$shipment) }}" class="mt-4 grid gap-3 rounded-2xl bg-slate-50 p-4">@csrf @method('PATCH')
<label class="text-sm font-bold">Update status<select name="status" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-3">@foreach(['picked_up'=>'Sudah dijemput','in_transit'=>'Dalam perjalanan','delivered'=>'Terkirim','failed'=>'Pengiriman gagal'] as $key=>$label)<option value="{{ $key }}" @selected($shipment->status===$key)>{{ $label }}</option>@endforeach</select></label>
<label class="text-sm font-bold">Catatan<textarea name="notes" rows="2" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2"></textarea></label>
<label class="text-sm font-bold">Foto bukti (maks. 4 MB)<input type="file" name="proof_photo" @required($shipment->status !== "delivered") accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white p-2"></label>
<button style="background:#0f766e;color:#fff" class="min-h-11 rounded-xl px-4 font-black">SIMPAN UPDATE</button></form>
@endif
</article>
@empty<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-black">Belum ada tugas pengiriman</p><p class="mt-1 text-sm text-slate-500">Tugas akan muncul setelah admin menugaskan kamu.</p></div>@endforelse
</div><div class="mt-5">{{ $shipments->links() }}</div>
@endsection
