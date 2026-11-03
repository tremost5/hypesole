<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#101b31">
    <link rel="manifest" href="{{ asset('admin-pwa/manifest.webmanifest') }}">
    <title>@yield('title', 'Dashboard') | HYPESOLE Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --admin-ink:#182338; --admin-muted:#64748b; --admin-navy:#101b31; --admin-blue:#2563eb; }
        html, body { min-height:100%; }
        body { background:#f3f6fb; color:var(--admin-ink); font-size:14px; }
        .app-wrapper { min-height:100vh; }
        .app-sidebar { background:var(--admin-navy)!important; box-shadow:2px 0 14px rgba(15,23,42,.08); }
        .sidebar-brand { min-height:58px; border-bottom:1px solid rgba(255,255,255,.10); }
        .brand-link { color:#fff!important; border:0!important; font-size:16px; letter-spacing:.02em; }
        .sidebar-wrapper { height:calc(100vh - 58px); overflow-y:auto; }
        .sidebar-menu .nav-link { margin:2px 8px; padding:9px 12px; border-radius:9px; color:#d7e0ef!important; font-size:13px; }
        .sidebar-menu .nav-link p { color:inherit!important; font-weight:500; }
        .sidebar-menu .nav-link .nav-icon { color:#a9bddb!important; font-size:15px; }
        .sidebar-menu .nav-link:hover { background:rgba(255,255,255,.09)!important; color:#fff!important; }
        .sidebar-menu .nav-link.active { background:#2563eb!important; color:#fff!important; }
        .sidebar-menu .nav-link.active .nav-icon { color:#fff!important; }
        .app-header { min-height:58px; border-bottom:1px solid #e5eaf2; }
        .app-header .nav-link { color:#334155!important; font-size:13px; }
        .app-content-header { padding:18px 0 8px; }
        .app-content-header h1,.app-content-header h3 { color:#17243a!important; font-size:21px; font-weight:700; }
        .app-content { padding-bottom:22px; }
        .app-content, .app-content p, .app-content label, .app-content td, .app-content th { color:inherit; }
        .app-content .text-white { color:#fff!important; }
        .app-content .text-slate-500,.app-content .text-secondary { color:#64748b!important; }
        .app-content .bg-white { background:#fff!important; }
        .app-content a { text-decoration:none; }
        .app-content input,.app-content select,.app-content textarea { color:#182338; }
        .app-footer { color:#64748b; font-size:12px; padding:12px 16px; }
        .card { border:1px solid #e7ecf3; border-radius:12px; box-shadow:0 2px 10px rgba(20,35,60,.045); }
        .btn { font-size:13px; }
        .table { font-size:13px; }
        .form-control,.form-select { font-size:13px; min-height:38px; }
        .alert { font-size:13px; }
        .admin-pwa-btn { border:1px solid #dbe3ef; background:#fff; color:#1e293b; border-radius:9px; padding:6px 10px; font-size:12px; }
        @media (max-width: 991.98px) {
            .app-sidebar { box-shadow:8px 0 24px rgba(15,23,42,.18); }
            .app-content-header { padding-top:14px; }
            .app-content { padding-left:10px; padding-right:10px; }
            .app-header .nav-link { padding-left:9px; padding-right:9px; }
        }
    </style>
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg">
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-white">
        <div class="container-fluid">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Buka atau tutup menu"><i class="bi bi-list fs-5"></i></a>
                </li>
                <li class="nav-item d-none d-sm-block"><span class="nav-link fw-semibold">HYPESOLE Management</span></li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><button type="button" class="admin-pwa-btn me-2" id="admin-pwa-install" hidden><i class="bi bi-phone me-1"></i><span class="d-none d-sm-inline">Install Admin</span><span class="d-sm-none">Install</span></button></li>
                <li class="nav-item"><span class="nav-link"><strong>{{ auth('web')->user()->name }}</strong><span class="text-secondary"> · {{ auth('web')->user()->role?->slug === 'owner' ? 'Superadmin' : auth('web')->user()->role?->name }}</span></span></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('admin.logout') }}">@csrf
                        <button class="btn btn-sm btn-outline-secondary ms-1">Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="brand-link"><span class="brand-text fw-bold">HYPESOLE <small class="text-info">ADMIN</small></span></a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard')?'active':'' }}"><i class="nav-icon bi bi-speedometer2"></i><p>Dashboard</p></a></li>
                    @php($links=[['users.view','admin.users.index','Akun Internal','bi-people'],['roles.view','admin.roles.index','Role & Permission','bi-shield-lock'],['catalog.view','admin.catalog.index','Kategori','bi-grid'],['services.view','admin.services.index','Layanan Shoe Care','bi-stars'],['products.view','admin.products.index','Produk Sneaker','bi-bag'],['orders.view','admin.shoe-care-orders.index','Order Shoe Care','bi-clipboard-check'],['store-orders.view','admin.store-orders.index','Pesanan Toko','bi-cart-check'],['shipments.view','admin.shipments.index','Kurir & Pengiriman','bi-truck'],['website.manage','admin.banners.index','Banner & Promo','bi-images'],['fonnte.manage','admin.fonnte.edit','WhatsApp Fonnte','bi-whatsapp']])
                    @foreach($links as [$perm,$route,$label,$ico])
                        @if(auth('web')->user()->hasPermission($perm) && \Illuminate\Support\Facades\Route::has($route))
                            <li class="nav-item"><a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($route)?'active':'' }}"><i class="nav-icon bi {{ $ico }}"></i><p>{{ $label }}</p></a></li>
                        @endif
                    @endforeach
                </ul>
            </nav>
        </div>
    </aside>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <h3 class="mb-0">@yield('heading', 'Dashboard')</h3>
                <small class="text-secondary">Panel pengelolaan HYPESOLE</small>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </div>
        </div>
    </main>
    <footer class="app-footer"><strong>HYPESOLE</strong> · AdminLTE 4 <span class="float-end">Admin Workspace</span></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
<script>
(() => {
    if ('serviceWorker' in navigator && window.isSecureContext) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('admin-pwa/service-worker.js') }}', { scope: '/admin/' })
                .catch(error => console.warn('HYPESOLE Admin PWA:', error));
        });
    }
    let promptEvent = null;
    const button = document.getElementById('admin-pwa-install');
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault(); promptEvent = event;
        if (button) button.hidden = false;
    });
    button?.addEventListener('click', async () => {
        if (!promptEvent) return;
        await promptEvent.prompt();
        await promptEvent.userChoice;
        promptEvent = null;
        button.hidden = true;
    });
})();
</script>
@stack('scripts')
</body>
</html>
