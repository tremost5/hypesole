@extends('admin.layout')
@section('title','Buat Pengiriman')
@section('content')
<div><p class="text-xs font-black tracking-widest text-teal-700">PENGATURAN LOGISTIK</p><h1 class="mt-1 text-3xl font-black">Pickup / Delivery</h1><p class="mt-2 text-sm text-slate-500">Order {{ $order->order_number }} · {{ $order->customer_name }}</p></div>
<form method="POST" action="{{ route('admin.shipments.store',$order) }}" class="mt-6 space-y-5 rounded-3xl border border-slate-200 bg-white p-5">@csrf
<div class="grid gap-4 sm:grid-cols-2">
<label class="text-sm font-bold">Jenis pengiriman<select name="type" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"><option value="pickup">Pickup dari pelanggan ke workshop</option><option value="delivery">Delivery dari workshop ke pelanggan</option></select></label>
<label class="text-sm font-bold">Metode<select name="method" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"><option value="internal">Kurir internal HYPESOLE</option><option value="external">Ekspedisi / kurir eksternal (luar kota)</option></select></label>
<label class="text-sm font-bold">Nama penerima / pengirim<input name="recipient_name" required value="{{ old('recipient_name',$order->customer_name) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold">Nomor WhatsApp<input name="recipient_phone" required value="{{ old('recipient_phone',$order->customer_phone) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold sm:col-span-2">Alamat lengkap<textarea name="address" required rows="3" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3">{{ old('address') }}</textarea></label>
<label class="text-sm font-bold">Kecamatan / Kota / Area<input name="area" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold">Jarak (km)<input name="distance_km" type="number" min="0" step="0.01" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold">Ongkir (Rp)<input name="shipping_fee" type="number" min="0" step="1000" required value="0" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold">Kurir internal<select name="courier_id" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"><option value="">Tugaskan nanti</option>@foreach($couriers as $courier)<option value="{{ $courier->id }}">{{ $courier->name }}</option>@endforeach</select></label>
<label class="text-sm font-bold">Nama ekspedisi<input name="carrier" placeholder="JNE, J&T, SiCepat, Pos..." class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold">Nomor resi<input name="tracking_number" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></label>
<label class="text-sm font-bold sm:col-span-2">Catatan<textarea name="notes" rows="2" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-3"></textarea></label>
</div>
<p class="rounded-xl bg-amber-50 p-3 text-sm text-amber-900">Ongkir dimasukkan manual oleh admin. Perhitungan otomatis berdasarkan tarif/rute belum diaktifkan; jarak 20 km harus diverifikasi berdasarkan rute jalan.</p>
<div class="flex flex-wrap gap-2"><button style="background:#0f766e;color:#fff" class="min-h-12 rounded-xl px-6 font-black">SIMPAN PENGIRIMAN</button><a href="{{ route('admin.shoe-care-orders.show',$order) }}" class="inline-flex min-h-12 items-center rounded-xl border border-slate-300 px-5 font-bold">BATAL</a></div>
</form>
@endsection
