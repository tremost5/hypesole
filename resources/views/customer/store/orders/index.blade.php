<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Sneaker Saya | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 px-4 py-4 text-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-3">
            <a href="{{ route('customer.dashboard') }}" class="font-black">HYPESOLE</a>
            <a href="{{ route('customer.dashboard') }}" style="background:#fff;color:#0f172a" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2 text-sm font-black">AKUN SAYA</a>
        </div>
    </header>
    <main class="mx-auto max-w-5xl px-4 py-8">
        <p class="text-xs font-bold tracking-widest text-cyan-800">SNEAKER STORE</p>
        <h1 class="mt-1 text-3xl font-black">Pesanan Sneaker Saya</h1>
        @if(session('success'))
            <p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        @endif
        <div class="mt-6 space-y-3">
            @forelse($orders as $order)
                <a href="{{ route('customer.store-orders.show', $order) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-cyan-700">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-cyan-800">{{ $order->order_number }}</p>
                            <h2 class="mt-1 font-black">{{ $order->recipient_name }}</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} item</p>
                        </div>
                        <span class="rounded-full bg-slate-950 px-3 py-2 text-xs font-black text-white">{{ str($order->status)->replace('_', ' ')->upper() }}</span>
                    </div>
                    <p class="mt-3 text-lg font-black">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</p>
                    <span class="mt-3 inline-flex min-h-10 items-center rounded-lg px-4 py-2 text-xs font-black" style="background:#e0f2fe;color:#075985">LIHAT DETAIL PESANAN →</span>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="font-bold">Belum ada pesanan sneaker.</p>
                    <a href="{{ route('home') }}" style="background:#0f172a;color:#fff" class="mt-4 inline-flex min-h-11 items-center rounded-xl px-5 py-3 text-sm font-black">MULAI BELANJA</a>
                </div>
            @endforelse
        </div>
        <div class="mt-5">{{ $orders->links() }}</div>
    </main>
</body>
</html>
