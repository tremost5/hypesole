<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya | HYPESOLE</title>
    <meta name="theme-color" content="#101827">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('customer.dashboard') }}" class="block">
                <p class="text-[10px] font-semibold tracking-[.3em] text-cyan-300">CARE AND SHOP</p>
                <h1 class="text-xl font-black tracking-wide">HYPESOLE</h1>
            </a>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button class="rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold transition hover:bg-white/10">Keluar</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-7 sm:px-6 sm:py-10">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-800 to-cyan-900 p-6 text-white shadow-xl sm:p-8">
            <p class="text-xs font-bold tracking-[.2em] text-cyan-200">CUSTOMER AREA</p>
            <h2 class="mt-2 text-2xl font-black sm:text-3xl">
                Halo, {{ $customer->name ?: 'Customer HYPESOLE' }}!
            </h2>
            <p class="mt-3 text-sm text-slate-300">Nomor WhatsApp terverifikasi: <span class="font-semibold text-white">{{ $customer->phone }}</span></p>
            <p class="mt-1 text-sm text-slate-300">Kelola profil, alamat, dan pantau status perawatan sepatu.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('customer.orders.index') }}" style="display:inline-flex;background:#fff;color:#0f172a;border:1px solid #fff" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm">ORDER SHOE CARE</a>
                <a href="{{ route('customer.store-orders.index') }}" style="display:inline-flex;background:#0891b2;color:#fff;border:1px solid #0891b2" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm">PESANAN SNEAKER</a>
                <a href="{{ route('home') }}" style="display:inline-flex;background:#1e293b;color:#fff;border:1px solid #64748b" class="inline-flex min-h-11 items-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm">BELANJA SNEAKER</a>
            </div>
        </section>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-bold">Periksa kembali data yang dimasukkan:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-7 grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
            <section class="h-fit rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5">
                    <p class="text-xs font-bold tracking-[.16em] text-cyan-800">AKUN SAYA</p>
                    <h3 class="mt-1 text-xl font-black">Profil customer</h3>
                    <p class="mt-1 text-sm text-slate-500">Lengkapi nama agar kami dapat menyapamu dengan benar.</p>
                </div>

                <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $customer->name) }}"
                            required
                            minlength="2"
                            maxlength="100"
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                        >
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Nomor WhatsApp</span>
                        <input
                            type="text"
                            value="{{ $customer->phone }}"
                            readonly
                            class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500"
                        >
                        <span class="mt-1 block text-xs text-slate-400">Nomor terverifikasi dan tidak dapat diubah dari halaman ini.</span>
                    </label>
                    <button type="submit" class="w-full rounded-xl bg-slate-950 px-5 py-3 font-bold text-white transition hover:bg-cyan-800">
                        Simpan Profil
                    </button>
                </form>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5">
                    <p class="text-xs font-bold tracking-[.16em] text-cyan-800">PENGIRIMAN & PICKUP</p>
                    <h3 class="mt-1 text-xl font-black">Tambah alamat</h3>
                    <p class="mt-1 text-sm text-slate-500">Simpan alamat rumah, kantor, atau lokasi pickup sepatu.</p>
                </div>

                <form method="POST" action="{{ route('customer.addresses.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Label alamat</span>
                        <input name="label" value="{{ old('label', 'Rumah') }}" maxlength="50" placeholder="Rumah / Kantor"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Nama penerima</span>
                        <input name="recipient_name" value="{{ old('recipient_name') }}" required maxlength="100" autocomplete="name" placeholder="Nama penerima"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label class="block sm:col-span-2">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Nomor WhatsApp penerima</span>
                        <input name="recipient_phone" value="{{ old('recipient_phone', $customer->phone) }}" required maxlength="24" inputmode="tel" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label class="block sm:col-span-2">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Alamat lengkap</span>
                        <textarea name="address_line" required rows="3" maxlength="2000" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('address_line') }}</textarea>
                    </label>
                    <label class="block sm:col-span-2">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Catatan alamat <span class="font-normal text-slate-400">(opsional)</span></span>
                        <textarea name="notes" rows="2" maxlength="1000" placeholder="Patokan rumah, warna pagar, instruksi kurir..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('notes') }}</textarea>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 sm:col-span-2">
                        <input type="checkbox" name="is_default" value="1" @checked(old('is_default')) class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                        Jadikan alamat utama
                    </label>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full rounded-xl bg-cyan-700 px-5 py-3 font-bold text-white transition hover:bg-cyan-800 sm:w-auto">
                            + Simpan Alamat
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <section class="mt-7">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                <div>
                    <p class="text-xs font-bold tracking-[.16em] text-cyan-800">BUKU ALAMAT</p>
                    <h3 class="mt-1 text-xl font-black">Alamat tersimpan</h3>
                </div>
                <span class="text-sm text-slate-500">{{ $customer->addresses->count() }} alamat</span>
            </div>

            @forelse ($customer->addresses as $address)
                <article class="mb-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $address->label }}</span>
                        @if ($address->is_default)
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Alamat utama</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('customer.addresses.update', $address) }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold text-slate-600">Label</span>
                            <input name="label" value="{{ $address->label }}" maxlength="50"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold text-slate-600">Nama penerima</span>
                            <input name="recipient_name" value="{{ $address->recipient_name }}" required maxlength="100"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-semibold text-slate-600">Nomor WhatsApp penerima</span>
                            <input name="recipient_phone" value="{{ $address->phone }}" required maxlength="24" inputmode="tel"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-semibold text-slate-600">Alamat lengkap</span>
                            <textarea name="address_line" required rows="3" maxlength="2000"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ $address->address_line }}</textarea>
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-semibold text-slate-600">Catatan</span>
                            <textarea name="notes" rows="2" maxlength="1000"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ $address->notes }}</textarea>
                        </label>
                        @if ($address->is_default)
                            <p class="text-xs font-medium text-emerald-700 sm:col-span-2">Alamat ini sedang menjadi alamat utama.</p>
                        @else
                            <label class="flex items-center gap-2 text-sm text-slate-600 sm:col-span-2">
                                <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                Jadikan alamat utama
                            </label>
                        @endif
                        <div class="flex flex-wrap gap-2 sm:col-span-2">
                            <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-cyan-800">Simpan Perubahan</button>
                        </div>
                    </form>

                    <div class="mt-3 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                        @unless ($address->is_default)
                            <form method="POST" action="{{ route('customer.addresses.default', $address) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-xl border border-cyan-200 px-4 py-2 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50">Jadikan Utama</button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Hapus Alamat</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                    <p class="font-bold text-slate-700">Belum ada alamat tersimpan</p>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan alamat untuk memudahkan pickup dan pengiriman sepatu.</p>
                </div>
            @endforelse
        </section>

        <section class="mt-7 grid gap-4 sm:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-semibold text-cyan-800">Shoe Care</p>
                <h3 class="mt-1 font-bold">Layanan perawatan sneaker</h3>
                <p class="mt-1 text-sm text-slate-500">Pemesanan cleaning dan restoration akan tersedia pada tahap berikutnya.</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-semibold text-cyan-800">Sneaker Store</p>
                <h3 class="mt-1 font-bold">Sneaker baru & preloved</h3>
                <p class="mt-1 text-sm text-slate-500">Lihat koleksi sneaker baru dan preloved.</p><a href="{{ route('home') }}" style="background:#0f172a;color:#fff" class="mt-3 inline-flex rounded-lg px-4 py-2 text-xs font-black">BUKA SNEAKER STORE</a>
            </article>
        </section>
    </main>
</body>
</html>
