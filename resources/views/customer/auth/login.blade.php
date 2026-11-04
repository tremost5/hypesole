<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Customer | HYPESOLE</title>
    <meta name="theme-color" content="#101827">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { min-height:100vh; background:radial-gradient(circle at 15% 10%,#274c77 0,transparent 35%),linear-gradient(135deg,#101827,#172b3a 55%,#123b45); }
        .auth-card { background:rgba(255,255,255,.96); box-shadow:0 28px 80px rgba(0,0,0,.28); }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center px-4 py-10 text-slate-900">
    <main class="w-full max-w-md">
        <div class="mb-8 text-center text-white">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-400 text-xl font-black text-slate-950">S</div>
            <p class="text-xs font-semibold tracking-[.35em] text-cyan-200">CARE AND SHOP</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">HYPESOLE</h1>
            <p class="mt-2 text-sm text-slate-300">Masuk untuk mengelola layanan dan pesanan sneaker kamu.</p>
        </div>

        <section class="auth-card rounded-3xl p-7 sm:p-8">
            <h2 class="text-xl font-bold">Masuk dengan WhatsApp</h2>
            <p class="mt-1 text-sm text-slate-500">Kami akan mengirim kode verifikasi 6 digit ke nomor WhatsApp kamu.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.otp.request') }}" class="mt-6 space-y-4">
                @csrf
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Nomor WhatsApp</span>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        inputmode="tel"
                        autocomplete="tel"
                        placeholder="08xxxxxxxxxx"
                        required
                        maxlength="24"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                    >
                </label>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-3.5 font-bold text-white transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                    Kirim Kode OTP
                </button>
            </form>
            <p class="mt-5 text-center text-xs leading-relaxed text-slate-500">
                Dengan masuk, kamu menyetujui penggunaan nomor WhatsApp untuk verifikasi akun dan informasi layanan HYPESOLE.
            </p>
        </section>
        <div class="mt-6 flex flex-col items-center gap-3 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span aria-hidden="true">←</span> Kembali ke HYPESOLE
            </a>
            <p class="text-xs text-slate-400">Premium sneaker experience, accessible for everyone.</p>
        </div>
    </main>
</body>
</html>
