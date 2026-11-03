@extends('admin.layout')
@section('title','Dashboard')
@section('heading','Dashboard')
@section('content')
<div class="card border-0 text-white" style="background:linear-gradient(120deg,#101b31,#17427b)">
    <div class="card-body p-4 p-lg-5">
        <div class="small fw-bold text-uppercase mb-2" style="letter-spacing:.16em;color:#b8d5ff">HYPESOLE WORKSPACE</div>
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
            <div><h1 class="h3 fw-bold text-white mb-2">Selamat datang, {{ auth('web')->user()->name }}</h1><p class="mb-0" style="color:#dbeafe">Ringkasan operasional HYPESOLE dalam satu tempat.</p></div>
            <span class="badge rounded-pill px-3 py-2" style="background:#ffffff24;color:#fff">{{ auth('web')->user()->role?->slug === 'owner' ? 'Superadmin' : auth('web')->user()->role?->name }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            @if(auth('web')->user()->hasPermission('orders.create'))<a href="{{ route('admin.shoe-care-orders.create') }}" class="btn btn-info btn-sm fw-semibold">+ Terima Shoe Care</a>@endif
            @if(auth('web')->user()->hasPermission('products.view'))<a href="{{ route('admin.products.index') }}" class="btn btn-outline-light btn-sm">Kelola Produk</a>@endif
        </div>
    </div>
</div>
<div class="row g-3 mt-1">
    @foreach([['Customer terdaftar',$customerCount,'bi-people','primary'],['Akun internal',$staffCount,'bi-person-badge','info'],['Akun aktif',$activeStaffCount,'bi-person-check','success'],['Role tersedia',$roleCount,'bi-shield-check','warning']] as [$label,$value,$icon,$color])
    <div class="col-12 col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="rounded-3 d-flex align-items-center justify-content-center bg-{{ $color }}-subtle text-{{ $color }}" style="width:44px;height:44px"><i class="bi {{ $icon }} fs-5"></i></div><div><div class="small text-secondary">{{ $label }}</div><div class="h4 fw-bold mb-0 text-dark">{{ number_format($value) }}</div></div></div></div></div>
    @endforeach
</div>
<div class="row g-3 mt-1">
    <div class="col-12 col-lg-6"><div class="card h-100"><div class="card-body p-4"><div class="small fw-bold text-primary text-uppercase">Operasional</div><h2 class="h5 fw-bold mt-1">Shoe Care</h2><p class="small text-secondary">Terima beberapa pasang sepatu dalam satu order dan pantau progres per sepatu.</p><div class="d-flex flex-wrap gap-2">@if(auth('web')->user()->hasPermission('orders.view'))<a href="{{ route('admin.shoe-care-orders.index') }}" class="btn btn-primary btn-sm">Lihat Order</a>@endif @if(auth('web')->user()->hasPermission('orders.create'))<a href="{{ route('admin.shoe-care-orders.create') }}" class="btn btn-outline-secondary btn-sm">Buat Order</a>@endif</div></div></div></div>
    <div class="col-12 col-lg-6"><div class="card h-100"><div class="card-body p-4"><div class="small fw-bold text-info text-uppercase">Retail</div><h2 class="h5 fw-bold mt-1">Sneaker Store</h2><p class="small text-secondary">Kelola koleksi sneaker, stok, dan pesanan pelanggan dari katalog publik.</p><div class="d-flex flex-wrap gap-2">@if(auth('web')->user()->hasPermission('products.view'))<a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">Kelola Produk</a>@endif @if(auth('web')->user()->hasPermission('store-orders.view'))<a href="{{ route('admin.store-orders.index') }}" class="btn btn-outline-secondary btn-sm">Pesanan Masuk</a>@endif</div></div></div></div></div>
</div>
@endsection
