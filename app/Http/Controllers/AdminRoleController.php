<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $slug = Str::slug($data['name']);
        if ($slug === 'owner' || Role::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'Nama role sudah digunakan atau tidak diperbolehkan.']);
        }

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Role berhasil dibuat.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->slug === 'owner', 403, 'Role Owner memiliki akses penuh dan tidak dapat diubah.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Role dan permission berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->slug === 'owner' || $role->is_system, 403, 'Role sistem tidak dapat dihapus.');

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Role masih digunakan akun internal. Pindahkan akun tersebut terlebih dahulu.']);
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}
