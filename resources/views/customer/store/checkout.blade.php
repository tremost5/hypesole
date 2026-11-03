<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Checkout | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 px-4 py-4 text-white">
        <div class="mx-auto max-w-5xl font-black">HYPESOLE · CHECKOUT</div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8">
        <h1 class="text-3xl font-black">Checkout</h1>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('customer.checkout.place') }}" class="mt-6 grid gap-5 lg:grid-cols-[1fr_340px]">
            @csrf
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black">Alamat penerima</h2>
                <div class="mt-4 space-y-3">
                    @foreach($addresses as $address)
                        <label class="flex cursor-pointer gap-3 rounded-xl border border-slate-200 p-4 hover:border-cyan-700">
                            <input type="radio" name="customer_address_id" value="{{ $address->id }}" required @checked(old('customer_address_id', $addresses->firstWhere('is_default', true)?->id) == $address->id)>
                            <span>
                                <strong>{{ $address->label ?: 'Alamat' }} · {{ $address->recipient_name }}</strong>
                                <span class="mt-1 block text-sm text-slate-500">{{ $address->phone }}<br>{{ $address->address_line }}</span>
                                @if($address->notes)
                                    <span class="mt-1 block text-xs text-slate-500">{{ $address->notes }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>

                <h2 class="mt-7 text-lg font-black">Metode pembayaran</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="rounded-xl border border-slate-200 p-4 hover:border-cyan-700">
                        <input type="radio" name="payment_method" value="transfer" @checked(old('payment_method', 'transfer') === 'transfer')>
                        <strong>Transfer</strong>
                        <p class="mt-1 text-xs text-slate-500">Instruksi transfer diberikan setelah toko mengonfirmasi pesanan.</p>
                    </label>
                    <label class="rounded-xl border border-slate-200 p-4 hover:border-cyan-700">
                        <input type="radio" name="payment_method" value="cash" @checked(old('payment_method') === 'cash')>
                        <strong>Cash</strong>
                        <p class="mt-1 text-xs text-slate-500">Pembayaran tunai saat pengambilan atau penyerahan.</p>
                    </label>
                </div>

                <label class="mt-5 block text-sm font-bold" for="customer_notes">Catatan pesanan</label>
                <textarea id="customer_notes" name="customer_notes" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 p-3">{{ old('customer_notes') }}</textarea>
            </section>

            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-black">Ringkasan pesanan</h2>
                <div class="mt-4 space-y-3">
                    @foreach($products as $product)
                        <div class="flex justify-between gap-3 text-sm">
                            <span>{{ $product->name }} × {{ session('store_cart')[$product->id] }}</span>
                            <strong>Rp{{ number_format((float) $product->price * session('store_cart')[$product->id], 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex justify-between text-sm"><span>Subtotal</span><strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    <div class="mt-2 flex justify-between text-sm"><span>Ongkir</span><strong>Rp0 (sementara)</strong></div>
                    <p class="mt-2 text-xs text-slate-500">Biaya pengiriman belum diterapkan pada tahap ini. Stok dikurangi saat pesanan dikonfirmasi admin.</p>
                </div>
                <button type="submit" style="background:#0f172a;color:#fff" class="mt-5 min-h-12 w-full rounded-xl px-4 py-3 text-sm font-black shadow-sm hover:bg-slate-800">BUAT PESANAN</button>
                <a href="{{ route('customer.cart.index') }}" style="background:#e2e8f0;color:#0f172a" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-xl px-4 py-2 text-sm font-bold">KEMBALI KE KERANJANG</a>
            </aside>
        </form>
    </main>
</body>
</html>
