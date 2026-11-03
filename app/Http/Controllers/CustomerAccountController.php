<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.max' => 'Nama maksimal 100 karakter.',
        ]);

        Auth::guard('customer')->user()->update([
            'name' => trim($validated['name']),
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $validated = $this->validateAddress($request);
        $phone = $this->normalizePhone($validated['recipient_phone']);

        if ($phone === null) {
            return back()->withErrors([
                'recipient_phone' => 'Nomor WhatsApp penerima tidak valid. Gunakan nomor Indonesia.',
            ])->withInput();
        }

        DB::transaction(function () use ($customer, $validated, $phone) {
            $makeDefault = (bool) ($validated['is_default'] ?? false)
                || ! $customer->addresses()->exists();

            if ($makeDefault) {
                $customer->addresses()->update(['is_default' => false]);
            }

            $customer->addresses()->create([
                'label' => trim(($validated['label'] ?? '') ?: 'Rumah'),
                'recipient_name' => trim($validated['recipient_name']),
                'phone' => $phone,
                'address_line' => trim($validated['address_line']),
                'notes' => isset($validated['notes']) ? trim($validated['notes']) : null,
                'is_default' => $makeDefault,
            ]);
        });

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $ownedAddress = $customer->addresses()->findOrFail($address->getKey());
        $validated = $this->validateAddress($request);
        $phone = $this->normalizePhone($validated['recipient_phone']);

        if ($phone === null) {
            return back()->withErrors([
                'recipient_phone' => 'Nomor WhatsApp penerima tidak valid. Gunakan nomor Indonesia.',
            ])->withInput();
        }

        DB::transaction(function () use ($customer, $ownedAddress, $validated, $phone) {
            $makeDefault = (bool) ($validated['is_default'] ?? false)
                || $ownedAddress->is_default
                || ! $customer->addresses()->where('is_default', true)->exists();

            if ($makeDefault) {
                $customer->addresses()->update(['is_default' => false]);
            }

            $ownedAddress->update([
                'label' => trim(($validated['label'] ?? '') ?: 'Rumah'),
                'recipient_name' => trim($validated['recipient_name']),
                'phone' => $phone,
                'address_line' => trim($validated['address_line']),
                'notes' => isset($validated['notes']) ? trim($validated['notes']) : null,
                'is_default' => $makeDefault,
            ]);
        });

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function setDefaultAddress(CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $ownedAddress = $customer->addresses()->findOrFail($address->getKey());

        DB::transaction(function () use ($customer, $ownedAddress) {
            $customer->addresses()->update(['is_default' => false]);
            $ownedAddress->update(['is_default' => true]);
        });

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function destroyAddress(CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $ownedAddress = $customer->addresses()->findOrFail($address->getKey());
        $wasDefault = $ownedAddress->is_default;

        DB::transaction(function () use ($customer, $ownedAddress, $wasDefault) {
            $ownedAddress->delete();

            if ($wasDefault) {
                $nextAddress = $customer->addresses()->latest('id')->first();

                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:24'],
            'address_line' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_phone.required' => 'Nomor WhatsApp penerima wajib diisi.',
            'address_line.required' => 'Alamat lengkap wajib diisi.',
        ]);
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0062')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if (! str_starts_with($digits, '62') || strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }
}
