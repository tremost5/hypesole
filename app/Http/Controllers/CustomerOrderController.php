<?php

namespace App\Http\Controllers;

use App\Models\ShoeCareOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = ShoeCareOrder::with('items')
            ->where('customer_id', $request->user('customer')->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function approveItem(Request $request, ShoeCareOrder $order, \App\Models\ShoeCareOrderItem $item)
    {
        abort_unless((int) $order->customer_id === (int) $request->user('customer')->id, 404);
        abort_unless($item->shoe_care_order_id === $order->id, 404);

        $data = $request->validate(['decision' => ['required', 'in:approved,rejected']]);
        abort_unless($item->customer_approval === 'pending' && $item->quoted_price !== null, 403);

        $item->update([
            'customer_approval' => $data['decision'],
            'customer_approved_at' => $data['decision'] === 'approved' ? now() : null,
        ]);

        \App\Models\ShoeCareOrderStatusLog::create([
            'shoe_care_order_id' => $order->id,
            'user_id' => null,
            'from_status' => null,
            'to_status' => $item->status,
            'note' => 'Pelanggan '.($data['decision'] === 'approved' ? 'menyetujui' : 'menolak').' penawaran item #'.$item->id.'.',
        ]);

        return back()->with('success', $data['decision'] === 'approved' ? 'Penawaran disetujui. Workshop dapat melanjutkan pengerjaan.' : 'Penawaran ditolak. Silakan hubungi workshop untuk tindak lanjut.');
    }

    public function show(Request $request, ShoeCareOrder $order): View
    {
        abort_unless((int) $order->customer_id === (int) $request->user('customer')->id, 404);
        $order->load(['statusLogs', 'items', 'shipments.courier']);

        return view('customer.orders.show', compact('order'));
    }
}
