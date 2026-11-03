@extends('admin.layout')
@section('title', 'Role & Permission')
@section('content')
    <div><p class="text-xs font-bold tracking-[.18em] text-cyan-800">ACCESS CONTROL</p><h1 class="mt-1 text-3xl font-black">Role & Permission</h1><p class="mt-2 text-sm text-slate-500">Atur hak akses berdasarkan tanggung jawab tim.</p></div>
    @if(auth('web')->user()->hasPermission('roles.create'))
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-lg font-black">Buat role baru</h2>
        <form method="POST" action="{{ route('admin.roles.store') }}" class="mt-4 space-y-3">@csrf
            <input name="name" required maxlength="80" placeholder="Nama role, misalnya Supervisor" class="w-full rounded-xl border border-slate-300 px-3 py-3">
            <input name="description" maxlength="255" placeholder="Deskripsi (opsional)" class="w-full rounded-xl border border-slate-300 px-3 py-3">
            <div class="grid gap-3 sm:grid-cols-2">@foreach($permissions as $group => $items)<div class="rounded-xl bg-slate-50 p-3"><h3 class="mb-2 text-sm font-black">{{ $group }}</h3>@foreach($items as $permission)<label class="mb-2 flex items-center gap-2 text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded">{{ $permission->name }}</label>@endforeach</div>@endforeach</div>
            <button class="rounded-xl bg-cyan-700 px-4 py-3 font-bold text-white">Simpan Role</button>
        </form>
    </section>
    @endif
    <section class="mt-6 space-y-4">
        @foreach($roles as $role)
        <article class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-2"><div><h2 class="font-black">{{ $role->name }}</h2><p class="text-xs text-slate-500">{{ $role->slug }} · {{ $role->users_count }} akun</p><p class="mt-1 text-sm text-slate-500">{{ $role->description }}</p></div>@if($role->slug === 'owner')<span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Full access</span>@endif</div>
            @if($role->slug !== 'owner' && auth('web')->user()->hasPermission('roles.update'))
            <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="mt-4 space-y-3">@csrf @method('PUT')
                <div class="grid gap-2 sm:grid-cols-2"><input name="name" value="{{ $role->name }}" required class="rounded-lg border border-slate-300 px-3 py-2"><input name="description" value="{{ $role->description }}" class="rounded-lg border border-slate-300 px-3 py-2"></div>
                <div class="grid gap-3 sm:grid-cols-2">@foreach($permissions as $group => $items)<div class="rounded-xl bg-slate-50 p-3"><h3 class="mb-2 text-sm font-black">{{ $group }}</h3>@foreach($items as $permission)<label class="mb-2 flex items-center gap-2 text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id)) class="rounded">{{ $permission->name }}</label>@endforeach</div>@endforeach</div>
                <button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-bold text-white">Simpan Permission</button>
            </form>
            @endif
            @if(!$role->is_system && $role->slug !== 'owner' && auth('web')->user()->hasPermission('roles.delete'))
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="mt-3" onsubmit="return confirm('Hapus role ini?')">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">Hapus role</button></form>
            @endif
        </article>
        @endforeach
    </section>
@endsection
