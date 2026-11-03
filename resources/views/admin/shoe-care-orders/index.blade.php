@extends('admin.layout')
@section('title','Order Shoe Care')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">OPERASIONAL SHOE CARE</p><h1 class="mt-1 text-3xl font-black">Order Shoe Care</h1><p class="mt-2 text-sm text-slate-500">Pantau penerimaan sepatu, pengerjaan, dan penyerahan kepada customer.</p></div>
@if(auth('web')->user()->hasPermission('orders.create'))<a href="{{ route('admin.shoe-care-orders.create') }}" style="background:#0f172a;color:#fff;border:1px solid #0f172a" class="inline-flex min-h-12 items-center justify-center rounded-xl px-5 py-3 text-sm font-black shadow">+ BUAT ORDER BARU</a>@endif</div>
<form method="GET" class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_220px_auto]">
<input name="q" value="{{ request('q') }}" placeholder="Cari nomor order, nama, atau WhatsApp" class="min-h-11 rounded-xl border border-slate-300 px-4 py-2.5">
<select name="status" class="min-h-11 rounded-xl border border-slate-300 px-4 py-2.5"><option value="">Semua status</option>@foreach($statuses as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select>
<button type="submit" style="background:#0f172a;color:#fff;border:1px solid #0f172a" class="min-h-11 rounded-xl px-5 py-2.5 text-sm font-black">CARI ORDER</button></form>
<div class="mt-5 space-y-3">@forelse($orders as $order)
<a href="{{ route('admin.shoe-care-orders.show',$order) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-cyan-400">
<div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold text-cyan-800">{{ $order->order_number }}</p><h2 class="mt-1 text-lg font-black">{{ $order->customer_name }}</h2><p class="text-sm text-slate-500">{{ $order->customer_phone }} · {{ $order->items->count() }} pasang sepatu</p></div><span class="rounded-full bg-slate-950 px-3 py-1.5 text-xs font-bold text-white">{{ $order->status_label }}</span></div>
<div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500"><span>Diterima: {{ $order->received_at?->format('d/m/Y H:i') ?? '-' }}</span><span>Staff: {{ $order->assignedStaff?->name ?? 'Belum ditugaskan' }}</span><span>Estimasi Rp{{ number_format((float)$order->items->sum('service_price'),0,',','.') }}</span></div></a>
@empty<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><h2 class="font-black">Belum ada order Shoe Care</h2><p class="mt-2 text-sm text-slate-500">Order baru akan muncul di daftar ini.</p></div>@endforelse</div>
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
