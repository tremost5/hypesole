<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestWhatsAppBookingController extends Controller
{
    public function create(): View
    {
        return view('booking.whatsapp', [
            'services' => Service::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_phone' => ['required', 'string', 'min:8', 'max:25'],
            'customer_address' => ['required', 'string', 'min:8', 'max:1000'],
            'pickup_method' => ['required', 'in:pickup,dropoff'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'customer_notes' => ['nullable', 'string', 'max:1500'],
            'items' => ['required', 'array', 'min:1', 'max:10'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.shoe_type' => ['required', 'string', 'max:80'],
            'items.*.shoe_brand' => ['nullable', 'string', 'max:100'],
            'items.*.shoe_model' => ['nullable', 'string', 'max:120'],
            'items.*.shoe_size' => ['nullable', 'string', 'max:20'],
            'items.*.shoe_color' => ['nullable', 'string', 'max:60'],
            'items.*.issue_description' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'items.max' => 'Maksimal 10 pasang sepatu untuk satu pengajuan.',
            'items.*.service_id.required' => 'Pilih layanan untuk setiap sepatu.',
            'items.*.issue_description.required' => 'Tuliskan kondisi atau keluhan setiap sepatu.',
        ]);

        $adminNumber = preg_replace('/\D+/', '', (string) config('hypesole.whatsapp_number'));
        if ($adminNumber === '') {
            return back()->withInput()->withErrors([
                'whatsapp' => 'Nomor WhatsApp HYPESOLE belum dikonfigurasi. Silakan hubungi admin melalui kontak yang tersedia.',
            ]);
        }
        if (Str::startsWith($adminNumber, '0')) {
            $adminNumber = '62'.substr($adminNumber, 1);
        } elseif (Str::startsWith($adminNumber, '8')) {
            $adminNumber = '62'.$adminNumber;
        }

        $services = Service::query()->where('is_active', true)
            ->whereIn('id', collect($data['items'])->pluck('service_id'))
            ->get()->keyBy('id');

        if ($services->count() !== collect($data['items'])->pluck('service_id')->unique()->count()) {
            return back()->withInput()->withErrors(['items' => 'Ada layanan yang tidak aktif. Silakan muat ulang halaman.']);
        }

        $phone = preg_replace('/\D+/', '', $data['customer_phone']);
        if (Str::startsWith($phone, '0')) $phone = '62'.substr($phone, 1);
        elseif (Str::startsWith($phone, '8')) $phone = '62'.$phone;

        $lines = [
            '*BOOKING SHOE CARE - HYPESOLE*',
            '',
            '*Data Pelanggan*',
            'Nama: '.$data['customer_name'],
            'WhatsApp: '.$data['customer_phone'].' ('.$phone.')',
            'Alamat: '.$data['customer_address'],
            'Metode: '.($data['pickup_method'] === 'pickup' ? 'Pickup / dijemput' : 'Antar sendiri ke workshop'),
            'Tanggal yang diinginkan: '.($data['preferred_date'] ?? 'Fleksibel / konfirmasi admin'),
            '',
            '*Detail Sepatu*',
        ];

        foreach ($data['items'] as $index => $item) {
            $service = $services->get($item['service_id']);
            $lines[] = '';
            $lines[] = 'Sepatu '.($index + 1).':';
            $lines[] = 'Layanan: '.$service->name;
            $lines[] = 'Jenis: '.$item['shoe_type'];
            $lines[] = 'Brand/Model: '.trim(($item['shoe_brand'] ?? '-').' '.($item['shoe_model'] ?? ''));
            $lines[] = 'Warna: '.($item['shoe_color'] ?? '-');
            $lines[] = 'Ukuran: '.($item['shoe_size'] ?? '-');
            $lines[] = 'Keluhan: '.$item['issue_description'];

            
        }
        if (!empty($data['customer_notes'])) {
            $lines[] = '';
            $lines[] = 'Catatan tambahan: '.$data['customer_notes'];
        }
        $lines[] = '';
        $lines[] = 'Mohon konfirmasi estimasi harga dan jadwal booking saya. Terima kasih.';

        return redirect()->away('https://wa.me/'.$adminNumber.'?text='.rawurlencode(implode("\n", $lines)));
    }

    

}
