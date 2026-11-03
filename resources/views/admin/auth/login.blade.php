<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101b31">
    <title>Login Internal | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body{background:radial-gradient(circle at top right,#1d4b86 0,#101b31 45%,#0b1426 100%);color:#17243a;font-size:14px}
        .login-card{width:min(100%,420px);background:#fff;border:1px solid #e4eaf2;border-radius:18px;box-shadow:0 24px 70px #02081766}
        .login-input{display:block;width:100%;height:44px;margin-top:7px;padding:0 13px;border:1px solid #cbd5e1;border-radius:9px;background:#fff;color:#17243a}
        .login-input:focus{outline:3px solid #bfdbfe;border-color:#2563eb}
        .login-label{display:block;font-size:13px;font-weight:600;color:#334155}
    </style>
</head>
<body class="flex min-h-screen items-center justify-center px-4 py-8">
    <main class="login-card p-6 sm:p-8">
        <div class="text-xs font-bold tracking-[.18em] text-blue-700">HYPESOLE CARE & SHOP</div>
        <h1 class="mt-2 text-2xl font-bold text-slate-900">Masuk ke Admin</h1>
        <p class="mt-1 text-sm leading-6 text-slate-600">Area khusus pengelola HYPESOLE. Hak akses mengikuti role akun.</p>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
            <div class="rounded-lg bg-amber-50 px-2 py-2 font-semibold text-amber-800">Superadmin</div>
            <div class="rounded-lg bg-blue-50 px-2 py-2 font-semibold text-blue-800">Admin</div>
            <div class="rounded-lg bg-emerald-50 px-2 py-2 font-semibold text-emerald-800">Kasir</div>
        </div>
        @if(session('success'))<div class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
            @csrf
            <label class="login-label">Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="login-input">
            </label>
            <label class="login-label">Password
                <input type="password" name="password" required autocomplete="current-password" class="login-input">
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded"> Ingat saya</label>
            <button class="w-full rounded-lg bg-blue-700 px-5 py-3 text-sm font-bold text-white hover:bg-blue-800">Masuk Dashboard</button>
        </form>
        <a href="{{ route('home') }}" class="mt-4 block text-center text-xs font-medium text-slate-500 hover:text-blue-700">Kembali ke website HYPESOLE</a>
    </main>
</body>
</html>
