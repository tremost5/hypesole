<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->order_number }} | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 px-4 py-4 text-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between gap-3">
            <a href="{{ route('customer.dashboard') }}" class="font-black">HYPESOLE</a>
            <a href="{{ route('customer.store-orders.index') }}" style="background:#fff;color:#0f172a" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2 text-sm font-black">PESANAN SAYA</a>
        </div>
    </header>
    <main class="mx-auto max-w-4xl px-4 py-8">
        <p class="text-xs font-bold tracking-widest text-cyan-800">DETAIL PESANAN</p>
        <h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ $order->order_number }}</h1>
        <section class="mt-5 rounded-2xl bg-slate-950 p-5 text-white">
            <p class="text-sm text-slate-300">Status pesanan</p>
            <p class="mt-1 text-2xl font-black">{{ str($order->status)->replace('_', ' ')->title() }}</p>
            <p class="mt-2 text-sm text-slate-300">Pembayaran: {{ str($order->payment_status)->replace('_', ' ')->title() }} · {{ ucfirst($order->payment_method) }}</p>
        </section>
        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-black">Produk dipesan</h2>
            @foreach($order->items as $item)
                <div class="flex justify-between gap-3 border-b border-slate-100 py-3 text-sm">
                    <div>
                        <p class="font-bold">{{ $item->product_name }}</p>
                        <p class="text-xs text-slate-500">SKU {{ $item->sku }} · {{ $item->product_type === 'new' ? 'Baru' : 'Preloved' }} · Qty {{ $item->quantity }} · Size {{ $item->size ?: '-' }}</p>
                    </div>
                    <strong class="shrink-0">Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="mt-4 flex justify-between text-lg font-black"><span>Total</span><span>Rp{{ number_format((float) $order->total, 0, ',', '.') }}</span></div>
        </section>
        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-black">Alamat penerima</h2>
            <p class="mt-3 text-sm font-bold">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</p>
            <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $order->shipping_address }}</p>
            @if($order->shipping_notes)
                <p class="mt-2 text-xs text-slate-500">{{ $order->shipping_notes }}</p>
            @endif
            @if($order->customer_notes)
                <p class="mt-3 text-sm"><strong>Catatan:</strong> {{ $order->customer_notes }}</p>
            @endif
        </section>
        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-black">Riwayat status</h2>
            <div class="mt-4 space-y-4">
                @forelse($order->statusLogs as $log)
                    <div class="border-l-2 border-cyan-600 pl-4">
                        <p class="font-bold">{{ str($log->to_status)->replace('_', ' ')->title() }}</p>
                        <p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                        @if($log->note)
                            <p class="mt-1 text-sm text-slate-600">{{ $log->note }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada pembaruan status.</p>
                @endforelse
            </div>
        </section>
        <a href="{{ route('customer.store-orders.index') }}" style="background:#0f172a;color:#fff" class="mt-5 inline-flex min-h-11 items-center rounded-xl px-5 py-3 text-sm font-black">KEMBALI KE PESANAN</a>
    </main>
</body>
</html>
