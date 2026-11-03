<?php

use App\Http\Controllers\GuestWhatsAppBookingController;
use Illuminate\Support\Facades\Route;

// Tambahkan dua route ini di routes/web.php, di luar middleware auth:customer.
Route::get('/booking/whatsapp', [GuestWhatsAppBookingController::class, 'create'])
    ->name('guest.booking.whatsapp.create');
Route::post('/booking/whatsapp', [GuestWhatsAppBookingController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('guest.booking.whatsapp.store');
