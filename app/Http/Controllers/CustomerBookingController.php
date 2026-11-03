<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ShoeCareOrder;
use App\Models\ShoeCareOrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerBookingController extends Controller
{
    public function create(): View
    {
        return view('customer.orders.create', [
            'services' => Service::query()->where('is_active', true)->orderBy('name')->get(),
            'customer' => request()->user('customer'),
        ]);
    }

    public function store(Request $request)
    {
        $customer = $request->user('customer');
        $data = $request->validate([
            'items' => ['required','array','min:1','max:10'],
            'items.*.service_id' => ['required','integer','exists:services,id'],
            'items.*.shoe_brand' => ['nullable','string','max:100'],
            'items.*.shoe_model' => ['nullable','string','max:150'],
            'items.*.shoe_color' => ['nullable','string','max:80'],
            'items.*.shoe_size' => ['nullable','string','max:30'],
            'items.*.issue_description' => ['required','string','min:5','max:3000'],
            'customer_notes' => ['nullable','string','max:2000'],
        ]);

        $services = Service::query()->whereIn('id', collect($data['items'])->pluck('service_id'))->where('is_active', true)->get()->keyBy('id');
        if ($services->count() !== collect($data['items'])->pluck('service_id')->unique()->count()) {
            throw ValidationException::withMessages(['items' => 'Ada layanan yang tidak aktif. Muat ulang halaman dan pilih layanan yang tersedia.']);
        }

        $order = DB::transaction(function () use ($data, $services, $customer) {
            do {
                $number = 'SC-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
            } while (ShoeCareOrder::where('order_number', $number)->exists());

            $first = $data['items'][0];
            $firstService = $services->get($first['service_id']);
            $order = ShoeCareOrder::create([
                'order_number' => $number,
                'customer_id' => $customer->id,
                'service_id' => $firstService->id,
                'received_by_user_id' => null,
                'customer_name' => $customer->name ?: 'Pelanggan',
                'customer_phone' => $customer->phone,
                'service_name' => $firstService->name,
                'service_price' => $firstService->price,
                'shoe_brand' => $first['shoe_brand'] ?? null,
                'shoe_model' => $first['shoe_model'] ?? null,
                'shoe_color' => $first['shoe_color'] ?? null,
                'shoe_size' => $first['shoe_size'] ?? null,
                'issue_description' => $first['issue_description'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'status' => 'received',
                'received_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $service = $services->get($item['service_id']);
                $order->items()->create([
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_price' => $service->price,
                    'shoe_brand' => $item['shoe_brand'] ?? null,
                    'shoe_model' => $item['shoe_model'] ?? null,
                    'shoe_color' => $item['shoe_color'] ?? null,
                    'shoe_size' => $item['shoe_size'] ?? null,
                    'issue_description' => $item['issue_description'],
                    'status' => 'received',
                    'customer_approval' => 'pending',
                ]);
            }

            ShoeCareOrderStatusLog::create([
                'shoe_care_order_id' => $order->id,
                'user_id' => null,
                'from_status' => null,
                'to_status' => 'received',
                'note' => 'Booking dibuat oleh pelanggan melalui website ('.count($data['items']).' pasang sepatu).',
            ]);
            return $order;
        });

        return redirect()->route('customer.orders.show', $order)->with('success', 'Booking berhasil dikirim. Workshop akan memeriksa sepatu dan mengirim penawaran harga.');
    }
}
