<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Keranjang | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 px-4 py-4 text-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="font-black">HYPESOLE</a>
            <a href="{{ route('home') }}" style="background:#fff;color:#0f172a" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2 text-sm font-black">LANJUT BELANJA</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8">
        <h1 class="text-3xl font-black">Keranjang Belanja</h1>

        @if(session('success'))
            <p class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-6 space-y-3">
            @forelse($lines as $line)
                <article class="grid grid-cols-[84px_1fr] gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[110px_1fr_auto]">
                    @if($line['product']->image_path)
                        <img src="{{ asset('storage/' . $line['product']->image_path) }}" alt="{{ $line['product']->name }}" class="aspect-square w-full rounded-xl object-cover">
                    @else
                        <div class="grid aspect-square place-items-center rounded-xl bg-slate-100 text-xs font-bold text-slate-400">FOTO</div>
                    @endif

                    <div class="min-w-0">
                        <h2 class="font-black">{{ $line['product']->name }}</h2>
                        <p class="text-xs text-slate-500">{{ $line['product']->sku }} · Stok {{ $line['product']->stock }}</p>
                        <p class="mt-2 font-bold">Rp{{ number_format((float) $line['product']->price, 0, ',', '.') }}</p>

                        <form method="POST" action="{{ route('customer.cart.update', $line['product']) }}" class="mt-3 flex flex-wrap items-center gap-2">
                            @csrf
                            @method('PUT')
                            <label for="quantity-{{ $line['product']->id }}" class="sr-only">Jumlah</label>
                            <input id="quantity-{{ $line['product']->id }}" type="number" name="quantity" min="0" max="{{ min(20, $line['product']->stock) }}" value="{{ $line['quantity'] }}" class="min-h-10 w-20 rounded-lg border border-slate-300 px-3 py-2">
                            <button type="submit" style="background:#0f172a;color:#fff" class="min-h-10 rounded-lg px-4 py-2 text-xs font-black">UPDATE JUMLAH</button>
                        </form>
                    </div>

                    <div class="col-span-2 flex items-center justify-between gap-3 sm:col-span-1 sm:flex-col sm:items-end">
                        <strong>Rp{{ number_format($line['line_total'], 0, ',', '.') }}</strong>
                        <form method="POST" action="{{ route('customer.cart.remove', $line['product']) }}" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:#fff1f2;color:#be123c;border:1px solid #fecdd3" class="min-h-10 rounded-lg px-4 py-2 text-xs font-black">HAPUS PRODUK</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="font-bold">Keranjang masih kosong.</p>
                    <a href="{{ route('home') }}" style="background:#0f172a;color:#fff" class="mt-4 inline-flex min-h-12 items-center rounded-xl px-5 py-3 text-sm font-black">LIHAT KATALOG</a>
                </div>
            @endforelse
        </div>

        @if(count($lines) > 0)
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 text-right shadow-sm">
                <p class="text-sm text-slate-500">Subtotal</p>
                <p class="text-2xl font-black">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                <a href="{{ route('customer.checkout') }}" style="background:#0f172a;color:#fff" class="mt-4 inline-flex min-h-12 items-center rounded-xl px-6 py-3 text-sm font-black">LANJUT CHECKOUT</a>
            </div>
        @endif
    </main>
</body>
</html>
