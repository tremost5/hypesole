@extends('admin.layout')
@section('title', 'Akun Internal')
@section('content')
    <div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">TEAM ACCESS</p><h1 class="mt-1 text-3xl font-black">Akun Internal</h1><p class="mt-2 text-sm text-slate-500">Kelola akun Owner, Admin, Staff, dan Courier.</p></div>
    @if(auth('web')->user()->hasPermission('users.create'))
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-lg font-black">Tambah akun</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
            @csrf
            <input name="name" required maxlength="120" placeholder="Nama lengkap" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="email" name="email" required placeholder="Email login" class="rounded-xl border border-slate-300 px-3 py-3">
            <select name="role_id" required class="rounded-xl border border-slate-300 px-3 py-3">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
            <input type="password" name="password" required minlength="12" placeholder="Password (min. 12 karakter)" class="rounded-xl border border-slate-300 px-3 py-3">
            <input type="password" name="password_confirmation" required minlength="12" placeholder="Ulangi password" class="rounded-xl border border-slate-300 px-3 py-3 sm:col-span-2">
            <button class="rounded-xl bg-cyan-700 px-4 py-3 font-bold text-white sm:col-span-2">Buat Akun</button>
        </form>
    </section>
    @endif
    <section class="mt-6 space-y-4">
        @forelse($users as $user)
        <article class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-2"><div><h2 class="font-black">{{ $user->name }}</h2><p class="text-sm text-slate-500">{{ $user->email }}</p></div><div class="flex gap-2"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold">{{ $user->role?->name ?? 'Tanpa role' }}</span><span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></div></div>
            @if(auth('web')->user()->hasPermission('users.update'))
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 grid gap-2 sm:grid-cols-4">
                @csrf @method('PUT')
                <input name="name" value="{{ $user->name }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <input type="email" name="email" value="{{ $user->email }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <select name="role_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">@foreach($roles as $role)<option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>@endforeach</select>
                <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="1" @selected($user->is_active)>Aktif</option><option value="0" @selected(!$user->is_active)>Nonaktif</option></select>
                <button class="rounded-lg bg-slate-950 px-3 py-2 text-sm font-bold text-white sm:col-span-4">Simpan Perubahan</button>
            </form>
            <details class="mt-3"><summary class="cursor-pointer text-sm font-semibold text-cyan-800">Ubah password</summary><form method="POST" action="{{ route('admin.users.password', $user) }}" class="mt-3 flex flex-wrap gap-2">@csrf @method('PUT')<input type="password" name="password" required minlength="12" placeholder="Password baru (min. 12)" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"><input type="password" name="password_confirmation" required minlength="12" placeholder="Ulangi password" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"><button class="rounded-lg border border-cyan-300 px-3 py-2 text-sm font-bold text-cyan-800">Update</button></form></details>
            @endif
            @if(auth('web')->user()->hasPermission('users.delete') && auth('web')->id() !== $user->id)
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }}?')">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">Hapus akun</button></form>
            @endif
        </article>
        @empty <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">Belum ada akun internal.</div> @endforelse
    </section>
@endsection
