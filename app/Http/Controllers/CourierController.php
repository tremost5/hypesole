<?php

namespace App\Http\Controllers;

use App\Models\ShoeCareShipment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CourierController extends Controller
{
    public function index(Request $request): View
    {
        $shipments = ShoeCareShipment::with('order')
            ->where('courier_id',$request->user('web')->id)
            ->latest()->paginate(20);
        return view('courier.dashboard', compact('shipments'));
    }

    public function updateStatus(Request $request, ShoeCareShipment $shipment): RedirectResponse
    {
        abort_unless((int)$shipment->courier_id === (int)$request->user('web')->id,404);
        $data = $request->validate([
            'status'=>['required',Rule::in(['picked_up','in_transit','delivered','failed'])],
            'notes'=>['nullable','string','max:2000'],
            'proof_photo'=>[\Illuminate\Validation\Rule::requiredIf(fn () => $request->input('status') === 'delivered'),'image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);
        if ($shipment->method !== 'internal') abort(403);
        AdminShipmentController::applyStatus($request,$shipment,$data,'web');
        return back()->with('success','Update pengiriman berhasil disimpan.');
    }
}
