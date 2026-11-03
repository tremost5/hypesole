<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\ShoeCareOrder;
use App\Models\ShoeCareOrderStatusLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminShoeCareOrderController extends Controller
{
    private const STATUSES = [
        'received' => 'Diterima',
        'inspection' => 'Pemeriksaan',
        'queued' => 'Menunggu antrean',
        'in_progress' => 'Sedang dikerjakan',
        'quality_check' => 'Pemeriksaan akhir',
        'completed' => 'Selesai dikerjakan',
        'ready_for_pickup' => 'Siap diambil',
        'delivered' => 'Sudah diserahkan',
        'cancelled' => 'Dibatalkan',
    ];

    public function index(Request $request): View
    {
        $orders = ShoeCareOrder::with(['assignedStaff', 'items'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->input('q'));
                $q->where(fn ($sub) => $sub->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_phone', 'like', "%{$term}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.shoe-care-orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.shoe-care-orders.create', [
            'services' => Service::with('category')->where('is_active', true)->orderBy('name')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'staff' => User::with('role')->where('is_active', true)
                ->whereHas('role', fn ($q) => $q->whereIn('slug', ['owner', 'admin', 'staff']))
                ->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:24'],
            'items' => ['required', 'array', 'min:1', 'max:10'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.shoe_brand' => ['nullable', 'string', 'max:100'],
            'items.*.shoe_model' => ['nullable', 'string', 'max:150'],
            'items.*.shoe_color' => ['nullable', 'string', 'max:80'],
            'items.*.shoe_size' => ['nullable', 'string', 'max:30'],
            'items.*.issue_description' => ['required', 'string', 'max:3000'],
            'items.*.internal_notes' => ['nullable', 'string', 'max:3000'],
            'items.*.assigned_staff_id' => ['nullable', 'integer', 'exists:users,id'],
            'items.*.before_photos' => ['nullable', 'array', 'max:5'],
            'items.*.before_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'estimated_completion_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $normalizedPhone = $this->normalizeIndonesianPhone($data['customer_phone']);
        $customer = !empty($data['customer_id'])
            ? Customer::findOrFail($data['customer_id'])
            : Customer::where('phone', $normalizedPhone)->first();

        if ($customer) {
            $data['customer_id'] = $customer->id;
            $data['customer_name'] = $customer->name ?: $data['customer_name'];
            $data['customer_phone'] = $customer->phone;
        } else {
            $data['customer_phone'] = $normalizedPhone;
        }

        $services = Service::whereIn('id', collect($data['items'])->pluck('service_id'))
            ->where('is_active', true)->get()->keyBy('id');
        if ($services->count() !== collect($data['items'])->pluck('service_id')->unique()->count()) {
            return back()->withErrors(['items' => 'Ada layanan yang sudah tidak aktif. Silakan pilih ulang.'])->withInput();
        }

        $order = DB::transaction(function () use ($data, $services, $request) {
            do {
                $number = 'SC-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
            } while (ShoeCareOrder::where('order_number', $number)->exists());

            $first = $data['items'][array_key_first($data['items'])];
            $firstService = $services->get($first['service_id']);
            $order = ShoeCareOrder::create([
                'order_number' => $number,
                'customer_id' => $data['customer_id'] ?? null,
                'service_id' => $firstService->id,
                'received_by_user_id' => $request->user('web')->id,
                'assigned_staff_id' => $first['assigned_staff_id'] ?? null,
                'customer_name' => trim($data['customer_name']),
                'customer_phone' => trim($data['customer_phone']),
                'service_name' => $firstService->name,
                'service_price' => $firstService->price,
                'shoe_brand' => $first['shoe_brand'] ?? null,
                'shoe_model' => $first['shoe_model'] ?? null,
                'shoe_color' => $first['shoe_color'] ?? null,
                'shoe_size' => $first['shoe_size'] ?? null,
                'issue_description' => $first['issue_description'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'internal_notes' => $first['internal_notes'] ?? null,
                'status' => 'received',
                'received_at' => now(),
                'estimated_completion_at' => $data['estimated_completion_at'] ?? null,
            ]);

            foreach ($data['items'] as $index => $item) {
                $service = $services->get($item['service_id']);
                $photos = [];
                foreach ($request->file("items.$index.before_photos", []) as $photo) {
                    $photos[] = $photo->store('shoe-care-orders/before', 'private');
                }

                $order->items()->create([
                    'service_id' => $service->id,
                    'assigned_staff_id' => $item['assigned_staff_id'] ?? null,
                    'service_name' => $service->name,
                    'service_price' => $service->price,
                    'shoe_brand' => $item['shoe_brand'] ?? null,
                    'shoe_model' => $item['shoe_model'] ?? null,
                    'shoe_color' => $item['shoe_color'] ?? null,
                    'shoe_size' => $item['shoe_size'] ?? null,
                    'issue_description' => $item['issue_description'],
                    'internal_notes' => $item['internal_notes'] ?? null,
                    'status' => 'received',
                    'before_photos' => $photos ?: null,
                ]);
            }

            ShoeCareOrderStatusLog::create([
                'shoe_care_order_id' => $order->id,
                'user_id' => $request->user('web')->id,
                'from_status' => null,
                'to_status' => 'received',
                'note' => 'Order diterima dengan '.count($data['items']).' item sepatu.',
            ]);

            return $order;
        });

        $message = 'Order berhasil dibuat: '.$order->order_number.' ('.$order->items()->count().' pasang sepatu).';
        $message .= $order->customer_id ? ' Terhubung ke akun customer.' : ' Nomor WhatsApp belum terdaftar sebagai akun customer.';
        return redirect()->route('admin.shoe-care-orders.show', $order)->with('success', $message);
    }

    public function show(ShoeCareOrder $order): View
    {
        $order->load(['assignedStaff', 'receivedBy', 'statusLogs.user', 'items.assignedStaff']);

        return view('admin.shoe-care-orders.show', [
            'order' => $order,
            'statuses' => self::STATUSES,
            'staff' => User::with('role')->where('is_active', true)
                ->whereHas('role', fn ($q) => $q->whereIn('slug', ['owner', 'admin', 'staff']))
                ->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, ShoeCareOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:received,inspection,queued,in_progress,quality_check,completed,ready_for_pickup,delivered,cancelled'],
            'assigned_staff_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'after_photos' => ['nullable', 'array', 'max:5'],
            'after_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $oldStatus = $order->status;
        $progressStatuses = ['queued', 'in_progress', 'quality_check', 'completed', 'ready_for_pickup', 'delivered'];
        if (in_array($data['status'], $progressStatuses, true)) {
            $pendingItems = $order->items()->where(function ($query) {
                $query->whereNull('customer_approval')->orWhere('customer_approval', '!=', 'approved');
            })->count();
            if ($pendingItems > 0) {
                return back()->withErrors(['status' => 'Order belum dapat diproses. Semua item sepatu harus disetujui pelanggan terlebih dahulu.']);
            }
        }

        DB::transaction(function () use ($request, $order, $data, $oldStatus) {
            $newStatus = $data['status'];
            $updates = [
                'status' => $newStatus,
                'assigned_staff_id' => $data['assigned_staff_id'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? $order->internal_notes,
            ];

            if ($newStatus === 'completed' && ! $order->completed_at) {
                $updates['completed_at'] = now();
            }
            if ($newStatus === 'delivered' && ! $order->delivered_at) {
                $updates['delivered_at'] = now();
            }

            $order->update($updates);

            if ($oldStatus !== $newStatus) {
                ShoeCareOrderStatusLog::create([
                    'shoe_care_order_id' => $order->id,
                    'user_id' => $request->user('web')->id,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus,
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        $photos = $this->savePhotos($request, 'after_photos', 'shoe-care-orders/after');
        if ($photos) {
            $order->update([
                'after_photos' => array_merge($order->fresh()->after_photos ?? [], $photos),
            ]);
        }

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    public function updateItemStatus(Request $request, ShoeCareOrder $order, \App\Models\ShoeCareOrderItem $item): RedirectResponse
    {
        abort_unless($item->shoe_care_order_id === $order->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:received,inspection,queued,in_progress,quality_check,completed,ready_for_pickup,delivered,cancelled'],
            'assigned_staff_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'after_photos' => ['nullable', 'array', 'max:5'],
            'after_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $progressStatuses = ['queued', 'in_progress', 'quality_check', 'completed', 'ready_for_pickup', 'delivered'];
        if (in_array($data['status'], $progressStatuses, true) && $item->customer_approval !== 'approved') {
            return back()->withErrors(['status' => 'Pengerjaan belum dapat dilanjutkan. Kirim penawaran harga dan tunggu persetujuan pelanggan terlebih dahulu.']);
        }

        $item->update(collect($data)->except('after_photos')->all());
        $photos = [];
        foreach ($request->file('after_photos', []) as $photo) {
            $photos[] = $photo->store('shoe-care-orders/after', 'private');
        }
        if ($photos) {
            $item->update(['after_photos' => array_merge($item->after_photos ?? [], $photos)]);
        }

        $statuses = $order->items()->pluck('status')->unique();
        if ($statuses->count() === 1) {
            $order->update(['status' => $statuses->first()]);
        }

        ShoeCareOrderStatusLog::create([
            'shoe_care_order_id' => $order->id,
            'user_id' => $request->user('web')->id,
            'from_status' => null,
            'to_status' => $data['status'],
            'note' => 'Update item sepatu: '.$item->shoe_brand.' '.$item->shoe_model,
        ]);

        return back()->with('success', 'Status item sepatu berhasil diperbarui.');
    }

    public function quoteItem(Request $request, ShoeCareOrder $order, \App\Models\ShoeCareOrderItem $item): RedirectResponse
    {
        abort_unless($item->shoe_care_order_id === $order->id, 404);
        $data = $request->validate([
            'quoted_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'inspection_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $item->update([
            'quoted_price' => $data['quoted_price'],
            'inspection_notes' => $data['inspection_notes'] ?? null,
            'customer_approval' => 'pending',
            'customer_approved_at' => null,
        ]);

        ShoeCareOrderStatusLog::create([
            'shoe_care_order_id' => $order->id,
            'user_id' => $request->user('web')->id,
            'from_status' => null,
            'to_status' => $item->status,
            'note' => 'Penawaran harga item sepatu #'.$item->id.' dikirim untuk persetujuan pelanggan.',
        ]);

        return back()->with('success', 'Penawaran harga berhasil disimpan. Menunggu persetujuan pelanggan.');
    }

    private function normalizeIndonesianPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0062')) {
            return '62'.substr($digits, 4);
        }

        if (str_starts_with($digits, '620')) {
            return '62'.substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    private function savePhotos(Request $request, string $field, string $folder): array
    {
        $saved = [];
        foreach ($request->file($field, []) as $file) {
            $saved[] = $file->store($folder, 'private');
        }

        return $saved;
    }
}
