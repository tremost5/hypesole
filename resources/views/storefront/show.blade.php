<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#07152f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="HYPESOLE">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <title>{{ $product->name }} | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 px-4 py-4 text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="font-black tracking-wide">HYPESOLE</a>
            <a href="{{ route('customer.cart.index') }}" style="background:#fff;color:#0f172a" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2 text-sm font-black">KERANJANG</a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center font-bold text-cyan-800">← Kembali ke katalog</a>

        <div class="mt-4 grid gap-7 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-2 md:p-7">
            <div class="overflow-hidden rounded-2xl bg-slate-100">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                @else
                    <div class="grid aspect-square place-items-center font-bold text-slate-400">FOTO PRODUK</div>
                @endif
            </div>

            <div class="flex flex-col">
                <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-black">
                    {{ $product->product_type === 'new' ? 'BARU' : 'PRELOVED' }}
                </span>
                <h1 class="mt-3 text-3xl font-black">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $product->brand }} · SKU {{ $product->sku }}</p>
                <p class="mt-5 text-2xl font-black">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm font-bold text-emerald-700">Stok tersedia: {{ $product->stock }}</p>

                @if($product->size)
                    <p class="mt-3 text-sm">Ukuran: <strong>{{ $product->size }}</strong></p>
                @endif

                @if($product->condition_note)
                    <div class="mt-4">
                        <p class="text-sm font-black">Kondisi</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $product->condition_note }}</p>
                    </div>
                @endif

                @if($product->description)
                    <div class="mt-4">
                        <p class="text-sm font-black">Deskripsi</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $product->description }}</p>
                    </div>
                @endif

                @if($product->stock > 0)
                    <form method="POST" action="{{ route('customer.cart.add', $product) }}" class="mt-auto pt-7">
                        @csrf
                        <label class="block text-sm font-bold" for="quantity">Jumlah</label>
                        <input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min(20, $product->stock) }}" required class="mt-2 block min-h-12 w-28 rounded-xl border border-slate-300 px-4 py-3">
                        <button type="submit" style="background:#0f172a;color:#fff" class="mt-4 min-h-12 w-full rounded-xl px-5 py-3 text-sm font-black shadow-sm hover:bg-slate-800">TAMBAH KE KERANJANG</button>
                    </form>
                @else
                    <div class="mt-7 rounded-xl bg-rose-100 p-4 text-center font-black text-rose-800">STOK HABIS</div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
