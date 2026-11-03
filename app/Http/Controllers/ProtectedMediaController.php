<?php

namespace App\Http\Controllers;

use App\Models\ShoeCareOrder;
use App\Models\ShoeCareOrderItem;
use App\Models\ShoeCareShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProtectedMediaController extends Controller
{
    public function orderPhoto(Request $request, ShoeCareOrder $order, ShoeCareOrderItem $item, string $kind, int $index): StreamedResponse
    {
        abort_unless($item->shoe_care_order_id === $order->id, 404);
        $this->authorizeOrder($request, $order);
        abort_unless(in_array($kind, ['before', 'after'], true), 404);
        $photos = $kind === 'before' ? ($item->before_photos ?? []) : ($item->after_photos ?? []);
        abort_unless(isset($photos[$index]), 404);
        return $this->stream($photos[$index]);
    }

    public function shipmentProof(Request $request, ShoeCareShipment $shipment): StreamedResponse
    {
        $order = $shipment->order;
        $this->authorizeOrder($request, $order);
        abort_unless($shipment->proof_photo_path, 404);
        return $this->stream($shipment->proof_photo_path);
    }

    private function authorizeOrder(Request $request, ShoeCareOrder $order): void
    {
        if ($request->user('customer')) {
            abort_unless((int) $order->customer_id === (int) $request->user('customer')->id, 404);
            return;
        }
        abort_unless($request->user('web'), 403);
        // Authenticated staff access is additionally constrained by the application's admin guard.
    }

    private function stream(string $path): StreamedResponse
    {
        $disk = Storage::disk('private')->exists($path) ? 'private' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $mime = Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream';
        abort_unless(str_starts_with($mime, 'image/'), 404);
        return response()->streamDownload(
            fn () => fpassthru(Storage::disk($disk)->readStream($path)),
            basename($path),
            ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']
        );
    }
}
