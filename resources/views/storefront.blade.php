<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#07152f">
    <meta name="description" content="HYPESOLE — Sneaker care, cleaning, restoration, dan sneaker shop. Premium sneaker care, restoration, dan curated sneaker store.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="HYPESOLE">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <title>HYPESOLE — Premium Sneaker Care & Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      :root{--ink:#07152f;--navy:#0a1b3d;--blue:#246bfe;--ice:#eaf2ff;--silver:#f4f7fc;--muted:#65738b}
      html{scroll-behavior:smooth;scroll-padding-top:90px}body{font-family:Inter,ui-sans-serif,system-ui,sans-serif;background:linear-gradient(180deg,#f8fbff 0%,#fff 46%,#f2f6ff 100%);color:var(--ink)}
      .hero-bg{isolation:isolate;background:radial-gradient(ellipse at 78% 20%,rgba(76,142,255,.42),transparent 32%),radial-gradient(ellipse at 12% 85%,rgba(110,169,255,.2),transparent 34%),linear-gradient(125deg,#050d20 0%,#0a1b3d 48%,#123e83 100%)}
      .hero-bg:before{content:"";position:absolute;inset:0;z-index:-1;opacity:.17;background-image:linear-gradient(rgba(255,255,255,.14) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.14) 1px,transparent 1px);background-size:54px 54px;mask-image:linear-gradient(90deg,black,transparent)}
      .glass{background:linear-gradient(135deg,rgba(255,255,255,.17),rgba(255,255,255,.055));backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.2);box-shadow:0 24px 80px rgba(0,0,0,.22)}
      .section-wrap{width:min(1220px,calc(100% - 36px));margin-inline:auto}
      .action{display:inline-flex;align-items:center;justify-content:center;min-height:50px;padding:0 22px;border-radius:15px;font-weight:850;font-size:13px;letter-spacing:.035em;transition:transform .22s,box-shadow .22s,background .22s}
      .action:focus-visible,a:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid #70a7ff;outline-offset:3px}
      .action-primary{background:linear-gradient(110deg,#dceaff 0%,#fff 48%,#a9caff 100%);color:#071a3b;box-shadow:0 10px 30px rgba(76,142,255,.2)}.action-primary:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(76,142,255,.32)}
      .action-whatsapp{gap:10px;background:linear-gradient(135deg,#25D366 0%,#16b957 55%,#0eaa4d 100%);border:1px solid rgba(255,255,255,.3);color:#fff!important;box-shadow:0 9px 24px rgba(37,211,102,.28),inset 0 1px 0 rgba(255,255,255,.22);text-shadow:0 1px 1px rgba(0,0,0,.12)}
      .action-whatsapp:hover{transform:translateY(-2px);background:linear-gradient(135deg,#35e477,#12b957);box-shadow:0 14px 30px rgba(37,211,102,.4)}
      .action-whatsapp svg{width:23px;height:23px;flex:none;display:block}
      .action-outline{border:1px solid rgba(255,255,255,.34);color:white;background:rgba(255,255,255,.07)}.action-outline:hover{background:rgba(255,255,255,.15);transform:translateY(-2px)}
      .service-card,.product-card{border:1px solid rgba(25,62,120,.08);box-shadow:0 12px 34px rgba(12,35,76,.055);transition:transform .24s,box-shadow .24s,border-color .24s}
      .service-card:hover,.product-card:hover{transform:translateY(-5px);box-shadow:0 24px 55px rgba(18,55,115,.13);border-color:rgba(36,107,254,.25)}
      #layanan,#koleksi{position:relative}#cara-kerja{background:linear-gradient(120deg,#eaf2ff,#f8fbff 48%,#e7efff)}
      .product-card .action{background:linear-gradient(110deg,#07152f,#174b9e);box-shadow:0 8px 20px rgba(7,21,47,.12)}
      .product-card .action:hover{background:linear-gradient(110deg,#174b9e,#347cff)}
      header{background:rgba(5,13,32,.9)!important;backdrop-filter:blur(18px)}
      footer{background:linear-gradient(115deg,#050d20,#0b1f45 62%,#123d80)!important}
      #pwa-install-button{background:linear-gradient(110deg,#07152f,#246bfe)!important}
      @media(max-width:640px){.section-wrap{width:calc(100% - 28px)}}
      @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*:before,*:after{transition:none!important}}
    </style>
</head>
<body class="min-h-screen bg-[#f5f7f6] text-slate-900">
<header class="sticky top-0 z-40 border-b border-white/10 bg-[#101820]/95 text-white backdrop-blur">
  <div class="section-wrap flex min-h-[70px] items-center justify-between gap-3">
    <a href="{{ route('home') }}" class="shrink-0 leading-tight">
      <span class="block text-[10px] font-bold tracking-[.28em] text-[#b8d3ff]">CARE • CLEAN • SHOP</span>
      <strong class="text-xl font-black tracking-[.06em]">HYPESOLE</strong>
    </a>
    <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-200 md:flex">
      <a class="hover:text-[#b8d3ff]" href="#layanan">Layanan</a><a class="hover:text-[#b8d3ff]" href="#koleksi">Sneaker Shop</a><a class="hover:text-[#b8d3ff]" href="#cara-kerja">Cara Kerja</a>
    </nav>
    <div class="flex items-center gap-2">
      <a href="{{ route('customer.login') }}" class="action action-outline !min-h-10 !px-3 !text-xs sm:!px-4">MASUK</a>
      <a href="{{ route('customer.cart.index') }}" class="action action-primary !min-h-10 !px-3 !text-xs sm:!px-4">KERANJANG</a>
    </div>
  </div>
</header>

<main>
@if(($banners ?? collect())->isNotEmpty())
<section class="section-wrap py-5"><div class="grid gap-4 md:grid-cols-2">@foreach($banners as $banner)<article class="overflow-hidden rounded-3xl bg-transparent"><img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" loading="lazy" class="mx-auto max-h-72 w-full object-contain"><div class="px-2 pb-3 pt-2 text-slate-900"><p class="text-xl font-black">{{ $banner->title }}</p>@if($banner->subtitle)<p class="mt-1 text-sm text-slate-600">{{ $banner->subtitle }}</p>@endif @if($banner->button_text && $banner->button_url)<a href="{{ $banner->button_url }}" class="action action-primary mt-3 w-fit">{{ $banner->button_text }}</a>@endif</div></article>@endforeach</div></section>
@endif
<section class="hero-bg relative overflow-hidden text-white">
 <div class="section-wrap grid min-h-[560px] items-center gap-10 py-14 md:grid-cols-[1.02fr_.98fr] md:py-20">
  <div class="relative z-10">
   <span class="glass inline-flex rounded-full px-4 py-2 text-[11px] font-extrabold tracking-[.2em] text-[#dceaff]">SNEAKER CARE & SHOP</span>
   <h1 class="mt-6 max-w-2xl text-[clamp(2.7rem,7vw,5.6rem)] font-black leading-[.98] tracking-[-.055em]">KEEP YOUR<br><span class="text-[#b8d3ff]">SNEAKERS</span><br>FRESH.</h1>
   <p class="mt-6 max-w-lg text-base leading-7 text-slate-300 sm:text-lg">Sneakers bersih, tampil percaya diri. Percayakan perawatan sepatu dan temukan koleksi pilihanmu di HYPESOLE.</p>
   <div class="mt-8 flex flex-col gap-3 min-[420px]:flex-row">
    <a href="{{ route('customer.login') }}" class="action action-primary">BOOKING ONLINE SEKARANG <span class="ml-2">↗</span></a>
    <a href="{{ route('guest.booking.whatsapp.create') }}" class="action action-whatsapp" aria-label="Booking via WhatsApp"><span><svg viewBox="0 0 32 32" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M16 3.2a12.1 12.1 0 0 0-10.3 18.5L4 28l6.5-1.7A12.1 12.1 0 1 0 16 3.2Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="M11.2 9.8c-.4-.8-.8-.8-1.2-.8h-1c-.4 0-.9.2-1.2.7-.4.5-1.6 1.6-1.6 3.8s1.6 4.4 1.8 4.7c.2.3 3.1 5 7.7 6.8 3.8 1.5 4.6 1.2 5.4 1.1.8-.1 2.6-1.1 3-2.1.4-1 .4-1.9.3-2.1-.1-.2-.4-.3-.9-.6l-3-1.5c-.4-.2-.7-.3-1 .2-.3.5-1.2 1.5-1.5 1.8-.3.3-.5.4-1 .1-.5-.2-2-.7-3.8-2.3-1.4-1.2-2.3-2.7-2.6-3.2-.3-.5 0-.7.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.3.1-.6 0-.8l-1.3-3.2Z" fill="currentColor" transform="translate(2 0) scale(.88)"/></svg></span><span>BOOKING VIA WHATSAPP</span></a>
    <a href="#koleksi" class="action action-outline">LIHAT PRODUK</a>
   </div>
   <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-xs font-semibold text-slate-300">
    <span>✦ Detail care berstandar premium</span><span>✦ Pickup & delivery terpantau</span><span>✦ Curated sneaker selection</span>
   </div>
  </div>
  <div class="relative mx-auto w-full max-w-[520px]">
   <div class="absolute -inset-5 rounded-[42px] bg-[#5794ff]/20 blur-2xl"></div>
   <div class="glass relative overflow-hidden rounded-[32px] p-3 shadow-2xl">
    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1100&q=85" alt="Sneaker merah premium" fetchpriority="high" class="h-[300px] w-full rounded-[24px] object-cover sm:h-[410px]">
    <div class="flex items-center justify-between gap-3 px-3 py-4">
     <div><p class="text-[10px] font-bold tracking-[.2em] text-[#b8d3ff]">YOUR SNEAKERS, OUR PASSION</p><p class="mt-1 text-lg font-black">Care that makes a difference.</p></div>
     <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-white to-blue-200 text-xl text-blue-950">✦</span>
    </div>
   </div>
  </div>
 </div>
</section>

<section id="layanan" class="section-wrap py-16 sm:py-20">
 <div class="mb-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
  <div><p class="text-xs font-black tracking-[.22em] text-blue-700">APA YANG KAMI TAWARKAN</p><h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Lebih dari sekadar cuci sepatu.</h2></div>
  <p class="max-w-md text-sm leading-6 text-slate-500">Rawat sneakers kesayangan atau temukan pasangan baru untuk melengkapi gayamu.</p>
 </div>
 <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <article class="service-card overflow-hidden rounded-3xl bg-white shadow-sm"><img src="https://images.unsplash.com/photo-1520256862855-398228c41684?auto=format&fit=crop&w=700&q=80" alt="Perawatan sneakers" loading="lazy" class="h-44 w-full object-cover"><div class="p-5"><p class="text-xs font-black tracking-widest text-blue-700">01 / CLEAN</p><h3 class="mt-2 text-lg font-black">Sneaker Cleaning</h3><p class="mt-2 text-sm leading-6 text-slate-500">Bersihkan sepatu agar kembali segar dan nyaman dipakai.</p></div></article>
  <article class="service-card overflow-hidden rounded-3xl bg-white shadow-sm"><img src="https://images.unsplash.com/photo-1600269470184-9a3f3f4f5c9a?auto=format&fit=crop&w=700&q=80" alt="Restorasi sepatu" loading="lazy" class="h-44 w-full object-cover"><div class="p-5"><p class="text-xs font-black tracking-widest text-blue-700">02 / RESTORE</p><h3 class="mt-2 text-lg font-black">Sneaker Restoration</h3><p class="mt-2 text-sm leading-6 text-slate-500">Perawatan dan pemulihan detail sepatu sesuai kondisi.</p></div></article>
  <article class="service-card overflow-hidden rounded-3xl bg-white shadow-sm"><img src="https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=700&q=80" alt="Koleksi sneaker" loading="lazy" class="h-44 w-full object-cover"><div class="p-5"><p class="text-xs font-black tracking-widest text-blue-700">03 / DISCOVER</p><h3 class="mt-2 text-lg font-black">Sneaker Shop</h3><p class="mt-2 text-sm leading-6 text-slate-500">Jelajahi koleksi sneakers baru dan preloved pilihan.</p></div></article>
  <article class="service-card overflow-hidden rounded-3xl bg-white shadow-sm"><img src="https://images.unsplash.com/photo-1580674285054-bed31e145f59?auto=format&fit=crop&w=700&q=80" alt="Paket pengiriman" loading="lazy" class="h-44 w-full object-cover"><div class="p-5"><p class="text-xs font-black tracking-widest text-blue-700">04 / DELIVER</p><h3 class="mt-2 text-lg font-black">Pickup & Delivery</h3><p class="mt-2 text-sm leading-6 text-slate-500">Antar jemput sepatu dengan informasi status perjalanan.</p></div></article>
 </div>
</section>

<section id="cara-kerja" class="bg-[#eaf2ff] py-16 sm:py-20">
 <div class="section-wrap">
  <div class="text-center"><p class="text-xs font-black tracking-[.22em] text-blue-700">SIMPLE & PRACTICAL</p><h2 class="mt-2 text-3xl font-black sm:text-4xl">Perawatan jadi lebih mudah.</h2><p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">Dari booking sampai sepatu kembali ke tanganmu, semua bisa dipantau.</p></div>
  <div class="mt-10 grid gap-4 md:grid-cols-3">
   <div class="rounded-3xl bg-white p-6"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#dceaff] font-black text-blue-900">01</span><h3 class="mt-4 font-black">Booking layanan</h3><p class="mt-2 text-sm leading-6 text-slate-500">Pilih layanan dan masukkan detail sepatu yang ingin dirawat.</p></div>
   <div class="rounded-3xl bg-white p-6"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#dceaff] font-black text-blue-900">02</span><h3 class="mt-4 font-black">Inspeksi & pengerjaan</h3><p class="mt-2 text-sm leading-6 text-slate-500">Workshop memeriksa kondisi, memberikan estimasi, lalu mengerjakan setelah disetujui.</p></div>
   <div class="rounded-3xl bg-white p-6"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#dceaff] font-black text-blue-900">03</span><h3 class="mt-4 font-black">Pantau & terima</h3><p class="mt-2 text-sm leading-6 text-slate-500">Pantau progres dan pilih pengambilan atau layanan pengantaran jika tersedia.</p></div>
  </div>
 </div>
</section>

<section id="koleksi" class="section-wrap py-16 sm:py-20">
 <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
  <div><p class="text-xs font-black tracking-[.22em] text-blue-700">CURATED FOR YOU</p><h2 class="mt-2 text-3xl font-black sm:text-4xl">Sneaker pilihan.</h2><p class="mt-2 text-sm text-slate-500">Temukan koleksi yang tersedia di HYPESOLE.</p></div>
  <span class="text-sm font-semibold text-slate-500">{{ $products->total() }} produk tersedia</span>
 </div>
 <form method="GET" action="{{ route('home') }}" class="mb-7 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_170px_190px_auto]">
  <input name="q" value="{{ request('q') }}" placeholder="Cari nama, brand, atau SKU..." aria-label="Cari produk" class="min-h-12 min-w-0 rounded-xl border border-slate-300 px-4 text-sm">
  <select name="type" aria-label="Jenis produk" class="min-h-12 rounded-xl border border-slate-300 px-3 text-sm"><option value="">Semua kondisi</option><option value="new" @selected(request('type') === 'new')>Baru</option><option value="preloved" @selected(request('type') === 'preloved')>Preloved</option></select>
  <select name="category" aria-label="Kategori" class="min-h-12 rounded-xl border border-slate-300 px-3 text-sm"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select>
  <button type="submit" class="min-h-12 rounded-xl bg-[#0b1d40] px-5 text-sm font-black text-white hover:bg-blue-900">CARI</button>
 </form>
 <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5">
  @forelse($products as $product)
   <article class="product-card overflow-hidden rounded-2xl border border-slate-200 bg-white">
    <a href="{{ route('storefront.products.show', $product) }}" class="block"><div class="aspect-square bg-slate-100">@if($product->image_path)<img src="{{ str_starts_with($product->image_path, 'images/demo/') ? asset($product->image_path) : asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">@else<div class="grid h-full place-items-center text-xs font-bold text-slate-400">FOTO PRODUK</div>@endif</div>
    <div class="p-3 sm:p-4"><span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black uppercase text-blue-900">{{ $product->product_type === 'new' ? 'BARU' : 'PRELOVED' }}</span><h3 class="mt-2 line-clamp-2 text-sm font-black sm:text-base">{{ $product->name }}</h3><p class="mt-1 text-xs text-slate-500">{{ $product->brand }}@if($product->size) · Size {{ $product->size }}@endif</p><p class="mt-3 text-sm font-black sm:text-base">Rp{{ number_format((float)$product->price,0,',','.') }}</p></div></a>
    <div class="px-3 pb-3 sm:px-4 sm:pb-4"><a href="{{ route('storefront.products.show',$product) }}" class="action min-h-10 w-full rounded-xl bg-[#0b1d40] px-3 text-xs text-white">LIHAT PRODUK</a></div>
   </article>
  @empty
   <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-bold">Produk belum tersedia.</p><p class="mt-1 text-sm text-slate-500">Silakan cek kembali nanti.</p></div>
  @endforelse
 </div>
 <div class="mt-7">{{ $products->links() }}</div>
</section>

<section id="hasil-care" class="bg-white py-16 sm:py-20">
 <div class="section-wrap">
  <div class="mb-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
   <div><p class="text-xs font-black tracking-[.22em] text-blue-700">REAL CARE, FRESH LOOK</p><h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Sneakers yang sudah kami rawat.</h2><p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">Lihat beberapa cerita perawatan sepatu dari pelanggan HYPESOLE.</p></div>
   <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-3 py-2 text-[11px] font-black tracking-wide text-amber-800">GALERI CONTOH / DEMO</span>
  </div>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
   @forelse($gallery as $galleryOrder)
    @php($galleryShoe = $galleryOrder->items->first())
    @php($galleryImage = $galleryShoe?->after_photos[0] ?? null)
    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
     <div class="aspect-[4/3] overflow-hidden bg-slate-100">
      @if($galleryImage && str_starts_with($galleryImage, 'images/demo/'))
       <img src="{{ asset($galleryImage) }}" alt="Hasil perawatan {{ $galleryOrder->shoe_brand }} {{ $galleryOrder->shoe_model }}" loading="lazy" class="h-full w-full object-cover">
      @else
       <div class="grid h-full place-items-center text-sm font-bold text-slate-400">HYPESOLE CARE</div>
      @endif
     </div>
     <div class="p-4">
      <p class="text-[10px] font-black uppercase tracking-[.18em] text-blue-700">{{ $galleryShoe?->service_name ?? $galleryOrder->service_name }}</p>
      <h3 class="mt-2 text-base font-black">{{ $galleryShoe?->shoe_brand ?? $galleryOrder->shoe_brand }} {{ $galleryShoe?->shoe_model ?? $galleryOrder->shoe_model }}</h3>
      <p class="mt-1 text-xs text-slate-500">{{ $galleryShoe?->shoe_color ?? $galleryOrder->shoe_color }} · Size {{ $galleryShoe?->shoe_size ?? $galleryOrder->shoe_size }}</p>
      <div class="mt-4 flex items-center gap-2 border-t border-slate-200 pt-3">
       <span class="grid h-8 w-8 place-items-center rounded-full bg-slate-900 text-xs font-black text-white">{{ strtoupper(substr($galleryOrder->customer_name, 0, 1)) }}</span>
       <div class="min-w-0"><p class="truncate text-xs font-bold text-slate-800">{{ $galleryOrder->customer_name }}</p><p class="text-[10px] text-slate-500">Pelanggan HYPESOLE</p></div>
       <span class="ml-auto rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-black text-emerald-700">SELESAI</span>
      </div>
     </div>
    </article>
   @empty
    <div class="col-span-full rounded-3xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">Galeri hasil perawatan akan tampil di sini.</div>
   @endforelse
  </div>
 </div>
</section>

<section class="section-wrap pb-16">
 <div class="rounded-[28px] bg-[#0b1d40] px-6 py-10 text-center text-white sm:px-12 sm:py-14"><p class="text-xs font-black tracking-[.22em] text-[#b8d3ff]">READY WHEN YOU ARE</p><h2 class="mx-auto mt-3 max-w-2xl text-3xl font-black sm:text-4xl">Sepatu favoritmu layak dirawat dengan baik.</h2><p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-300">Mulai booking perawatan atau temukan sneakers berikutnya hari ini.</p><div class="mt-7 flex flex-col justify-center gap-3 min-[420px]:flex-row min-[420px]:flex-wrap"><a href="{{ route('customer.login') }}" class="action action-primary">BOOKING ONLINE SEKARANG</a><a href="{{ route('guest.booking.whatsapp.create') }}" class="action action-whatsapp" aria-label="Booking via WhatsApp"><span><svg viewBox="0 0 32 32" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M16 3.2a12.1 12.1 0 0 0-10.3 18.5L4 28l6.5-1.7A12.1 12.1 0 1 0 16 3.2Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="M11.2 9.8c-.4-.8-.8-.8-1.2-.8h-1c-.4 0-.9.2-1.2.7-.4.5-1.6 1.6-1.6 3.8s1.6 4.4 1.8 4.7c.2.3 3.1 5 7.7 6.8 3.8 1.5 4.6 1.2 5.4 1.1.8-.1 2.6-1.1 3-2.1.4-1 .4-1.9.3-2.1-.1-.2-.4-.3-.9-.6l-3-1.5c-.4-.2-.7-.3-1 .2-.3.5-1.2 1.5-1.5 1.8-.3.3-.5.4-1 .1-.5-.2-2-.7-3.8-2.3-1.4-1.2-2.3-2.7-2.6-3.2-.3-.5 0-.7.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.3.1-.6 0-.8l-1.3-3.2Z" fill="currentColor" transform="translate(2 0) scale(.88)"/></svg></span><span>BOOKING VIA WHATSAPP</span></a><a href="#koleksi" class="action action-outline">LIHAT PRODUK</a></div></div>
</section>
</main>

<footer class="bg-[#101820] py-9 text-white">
 <div class="section-wrap flex flex-col justify-between gap-5 sm:flex-row sm:items-center"><div><strong class="text-lg font-black tracking-widest">HYPESOLE</strong><p class="mt-1 text-sm text-slate-400">Premium Sneaker Care & Store</p></div><div class="text-sm text-slate-400 sm:text-right"><p>© {{ date('Y') }} HYPESOLE. All rights reserved.</p><p class="mt-1">Kharisnet : <a class="font-bold text-[#b8d3ff]" href="https://malang-web.site" target="_blank" rel="noopener">malang-web.site</a></p></div></div>
</footer>
<div id="pwa-install-banner" class="fixed inset-x-3 bottom-3 z-50 mx-auto hidden max-w-md rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl">
 <div class="flex items-center gap-3"><img src="{{ asset('icons/icon-192.png') }}" alt="" class="h-12 w-12 rounded-xl"><div class="min-w-0 flex-1"><p class="font-black text-slate-900">Pasang HYPESOLE</p><p class="text-xs text-slate-500">Akses cepat dari layar utama, tanpa APK.</p></div><button id="pwa-install-close" type="button" aria-label="Tutup" class="rounded-lg px-3 py-2 text-slate-500">✕</button></div>
 <button id="pwa-install-button" type="button" class="mt-3 min-h-11 w-full rounded-xl bg-[#0b1d40] px-4 font-black text-white">TAMBAHKAN KE LAYAR UTAMA</button>
 <p id="pwa-install-help" class="mt-2 hidden text-xs leading-5 text-slate-600">Jika jendela pemasangan tidak muncul: di Chrome Android buka menu ⋮ lalu pilih <strong>Install app</strong> atau <strong>Tambahkan ke layar utama</strong>. Di Chrome desktop, gunakan ikon Install di bilah alamat atau menu browser.</p>
 <div id="pwa-ios-instructions" class="mt-2 hidden rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600">Di Safari iPhone/iPad, tekan tombol <strong>Bagikan</strong>, lalu pilih <strong>Tambahkan ke Layar Utama</strong>.</div>
</div>
</body>
</html>
