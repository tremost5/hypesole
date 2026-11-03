<?php

namespace App\Http\Controllers;

use App\Models\ShoeCareOrder;
use App\Models\ShoeCareShipment;
use App\Models\ShoeCareShipmentEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminShipmentController extends Controller
{
    public function index(): View
    {
        $shipments = ShoeCareShipment::with(['order','courier'])->latest()->paginate(20);
        return view('admin.shipments.index', compact('shipments'));
    }

    public function create(ShoeCareOrder $order): View
    {
        $couriers = User::with('role')->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('slug', 'courier'))
            ->orderBy('name')->get();

        return view('admin.shipments.create', compact('order','couriers'));
    }

    public function store(Request $request, ShoeCareOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['pickup','delivery'])],
            'method' => ['required', Rule::in(['internal','external'])],
            'courier_id' => ['nullable','exists:users,id'],
            'recipient_name' => ['required','string','max:120'],
            'recipient_phone' => ['required','string','max:24'],
            'address' => ['required','string','max:3000'],
            'area' => ['nullable','string','max:120'],
            'distance_km' => ['nullable','numeric','min:0','max:99999'],
            'shipping_fee' => ['required','numeric','min:0','max:999999999'],
            'carrier' => ['nullable','required_if:method,external','string','max:100'],
            'tracking_number' => ['nullable','string','max:120'],
            'notes' => ['nullable','string','max:2000'],
        ]);

        if (($data['method'] ?? '') === 'internal' && !empty($data['courier_id'])) {
            abort_unless(User::whereKey($data['courier_id'])->whereHas('role', fn($q) => $q->where('slug','courier'))->exists(), 422);
        }

        DB::transaction(function () use ($data, $order, $request) {
            $shipment = ShoeCareShipment::create($data + [
                'shoe_care_order_id' => $order->id,
                'status' => !empty($data['courier_id']) || $data['method'] === 'external' ? 'assigned' : 'waiting_assignment',
                'assigned_at' => !empty($data['courier_id']) || $data['method'] === 'external' ? now() : null,
            ]);
            ShoeCareShipmentEvent::create([
                'shoe_care_shipment_id' => $shipment->id,
                'user_id' => $request->user('web')->id,
                'status' => $shipment->status,
                'description' => 'Pengiriman dibuat: '.$shipment->type_label.'.',
            ]);
        });

        return redirect()->route('admin.shipments.index')->with('success','Pengiriman berhasil dibuat.');
    }

    public function assign(Request $request, ShoeCareShipment $shipment): RedirectResponse
    {
        $data = $request->validate(['courier_id'=>['required','exists:users,id']]);
        abort_unless(User::whereKey($data['courier_id'])->whereHas('role',fn($q)=>$q->where('slug','courier'))->exists(), 422);
        abort_unless($shipment->method === 'internal', 422);

        $shipment->update(['courier_id'=>$data['courier_id'],'status'=>'assigned','assigned_at'=>now()]);
        ShoeCareShipmentEvent::create([
            'shoe_care_shipment_id'=>$shipment->id,'user_id'=>$request->user('web')->id,
            'status'=>'assigned','description'=>'Kurir ditugaskan: '.User::find($data['courier_id'])->name,
        ]);
        return back()->with('success','Kurir berhasil ditugaskan.');
    }

    public function updateStatus(Request $request, ShoeCareShipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'status'=>['required',Rule::in(['waiting_assignment','assigned','picked_up','in_transit','delivered','failed','cancelled'])],
            'notes'=>['nullable','string','max:2000'],
            'tracking_number'=>['nullable','string','max:120'],
            'proof_photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);
        $this->applyStatus($request, $shipment, $data, 'web');
        return back()->with('success','Status pengiriman diperbarui.');
    }

    public static function applyStatus(Request $request, ShoeCareShipment $shipment, array $data, string $guard): void
    {
        $path = $request->file('proof_photo')?->store('shoe-care-shipments/proofs','private');
        $updates = ['status'=>$data['status'],'notes'=>$data['notes'] ?? $shipment->notes];
        if (array_key_exists('tracking_number',$data)) $updates['tracking_number']=$data['tracking_number'];
        if ($path) $updates['proof_photo_path']=$path;
        if ($data['status'] === 'picked_up' && !$shipment->picked_up_at) $updates['picked_up_at']=now();
        if ($data['status'] === 'delivered' && !$shipment->delivered_at) $updates['delivered_at']=now();
        $shipment->update($updates);
        ShoeCareShipmentEvent::create([
            'shoe_care_shipment_id'=>$shipment->id,
            'user_id'=>$request->user($guard)?->id,
            'status'=>$data['status'],
            'description'=>$data['notes'] ?? null,
            'proof_photo_path'=>$path,
        ]);
    }
}
