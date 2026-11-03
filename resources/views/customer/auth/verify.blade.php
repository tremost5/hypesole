<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP | HYPESOLE</title>
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
        </div>

        <section class="auth-card rounded-3xl p-7 sm:p-8">
            <h2 class="text-xl font-bold">Verifikasi nomor WhatsApp</h2>
            <p class="mt-1 text-sm text-slate-500">Masukkan kode 6 digit yang dikirim ke WhatsApp kamu. Kode berlaku selama 5 menit.</p>

            @if (session('status'))
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.otp.verify') }}" class="mt-6 space-y-4">
                @csrf
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Kode OTP</span>
                    <input
                        type="text"
                        name="otp"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        minlength="6"
                        maxlength="6"
                        placeholder="••••••"
                        required
                        autofocus
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-4 text-center text-2xl font-bold tracking-[.5em] outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                    >
                </label>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-3.5 font-bold text-white transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                    Verifikasi & Masuk
                </button>
            </form>

            <form method="POST" action="{{ route('customer.otp.request') }}" class="mt-3">
                @csrf
                <input type="hidden" name="phone" value="{{ session('customer_otp_phone') }}">
                <button type="submit" class="w-full rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Kirim ulang kode
                </button>
            </form>
            <a href="{{ route('customer.login') }}" class="mt-4 block text-center text-sm font-semibold text-cyan-800 hover:underline">Ganti nomor WhatsApp</a>
        </section>
    </main>
</body>
</html>
