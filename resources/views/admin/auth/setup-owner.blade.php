<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Owner | HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10">
    <div class="w-full max-w-lg rounded-3xl bg-white p-7 shadow-2xl sm:p-9">
        <div class="text-xs font-black tracking-[.24em] text-cyan-700">INITIAL SECURITY SETUP</div>
        <h1 class="mt-3 text-3xl font-black">Buat Owner pertama</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Halaman ini hanya tersedia sebelum akun Owner dibuat. Gunakan password kuat minimal 12 karakter.</p>
        @if($errors->any())<div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.setup-owner.store') }}" class="mt-7 space-y-4">
            @csrf
            <label class="block text-sm font-semibold">Nama lengkap
                <input name="name" value="{{ old('name') }}" required maxlength="120" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
            </label>
            <label class="block text-sm font-semibold">Email login
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
            </label>
            <label class="block text-sm font-semibold">Password
                <input type="password" name="password" required minlength="12" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
            </label>
            <label class="block text-sm font-semibold">Ulangi password
                <input type="password" name="password_confirmation" required minlength="12" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
            </label>
            <button class="w-full rounded-xl bg-cyan-700 px-5 py-3 font-bold text-white hover:bg-cyan-800">Buat Akun Owner</button>
        </form>
    </div>
</body>
</html>
