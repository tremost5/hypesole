<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('role')->orderBy('name')->get(),
            'roles' => Role::when(auth('web')->user()->role?->slug !== 'owner', fn ($q) => $q->where('slug', '!=', 'owner'))->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $targetRole = Role::findOrFail($data['role_id']);
        abort_if($targetRole->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner', 403, 'Hanya Superadmin yang dapat membuat akun Superadmin.');

        User::create([
            'name' => trim($data['name']),
            'email' => strtolower($data['email']),
            'role_id' => $data['role_id'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        return back()->with('success', 'Akun internal berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($user->role?->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner') {
            abort(403, 'Akun Superadmin hanya dapat dikelola oleh Superadmin.');
        }
        $targetRole = Role::findOrFail($data['role_id']);
        if ($targetRole->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner') {
            abort(403, 'Hanya Superadmin yang dapat menetapkan role Superadmin.');
        }

        if ($user->id === Auth::guard('web')->id() && (! $data['is_active'] || (int) $data['role_id'] !== (int) $user->role_id)) {
            return back()->withErrors(['account' => 'Kamu tidak dapat menonaktifkan atau mengganti role akun sendiri.']);
        }

        if ($user->role?->slug === 'owner' && ((int) $data['role_id'] !== (int) $user->role_id || ! $data['is_active'])) {
            $otherActiveOwners = User::where('role_id', $user->role_id)->where('is_active', true)->where('id', '!=', $user->id)->exists();
            if (! $otherActiveOwners) {
                return back()->withErrors(['account' => 'Minimal harus ada satu Owner aktif.']);
            }
        }

        $user->update([
            'name' => trim($data['name']),
            'email' => strtolower($data['email']),
            'role_id' => $data['role_id'],
            'is_active' => $data['is_active'],
        ]);

        return back()->with('success', 'Akun internal berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password akun berhasil diubah.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role?->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner') {
            abort(403, 'Akun Superadmin hanya dapat dikelola oleh Superadmin.');
        }
        $targetRole = Role::findOrFail($data['role_id']);
        if ($targetRole->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner') {
            abort(403, 'Hanya Superadmin yang dapat menetapkan role Superadmin.');
        }

        if ($user->role?->slug === 'owner' && auth('web')->user()->role?->slug !== 'owner') {
            abort(403, 'Akun Superadmin hanya dapat dihapus oleh Superadmin.');
        }
        if ($user->id === Auth::guard('web')->id()) {
            return back()->withErrors(['account' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
        }

        if ($user->role?->slug === 'owner') {
            $otherActiveOwners = User::where('role_id', $user->role_id)->where('is_active', true)->where('id', '!=', $user->id)->exists();
            if (! $otherActiveOwners) {
                return back()->withErrors(['account' => 'Owner aktif terakhir tidak dapat dihapus.']);
            }
        }

        $user->delete();

        return back()->with('success', 'Akun internal berhasil dihapus.');
    }
}
