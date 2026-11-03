<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\FonnteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    private const OTP_TTL_MINUTES = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const MAX_OTP_REQUESTS_PER_HOUR = 5;
    private const MAX_OTP_ATTEMPTS = 5;

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('customer.auth.login');
    }

    public function requestOtp(Request $request, FonnteService $fonnte): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:24'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        if ($phone === null) {
            return back()
                ->withErrors(['phone' => 'Nomor WhatsApp tidak valid. Gunakan nomor Indonesia, misalnya 081234567890.'])
                ->withInput();
        }

        $ipKey = 'customer-otp-request:ip:'.hash('sha256', (string) $request->ip());

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            return back()->withErrors([
                'phone' => 'Terlalu banyak permintaan dari perangkat ini. Coba lagi nanti.',
            ])->withInput(['phone' => $validated['phone']]);
        }

        RateLimiter::hit($ipKey, 3600);

        $now = now();
        $requestsThisHour = CustomerOtp::query()
            ->where('phone', $phone)
            ->where('created_at', '>=', $now->copy()->subHour())
            ->count();

        if ($requestsThisHour >= self::MAX_OTP_REQUESTS_PER_HOUR) {
            return back()->withErrors([
                'phone' => 'Batas permintaan kode sudah tercapai. Coba lagi setelah satu jam.',
            ])->withInput(['phone' => $validated['phone']]);
        }

        $latestRequest = CustomerOtp::query()
            ->where('phone', $phone)
            ->latest('id')
            ->first();

        if ($latestRequest && $latestRequest->created_at->diffInSeconds($now) < self::RESEND_COOLDOWN_SECONDS) {
            return back()->withErrors([
                'phone' => 'Tunggu 60 detik sebelum meminta kode baru.',
            ])->withInput(['phone' => $validated['phone']]);
        }

        $code = (string) random_int(100000, 999999);

        $otp = DB::transaction(function () use ($phone, $code, $request) {
            CustomerOtp::query()
                ->where('phone', $phone)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now(), 'updated_at' => now()]);

            return CustomerOtp::create([
                'phone' => $phone,
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'delivery_status' => 'pending',
                'ip_address' => $request->ip(),
            ]);
        });

        if (! $fonnte->sendOtp($phone, $code)) {
            $otp->forceFill([
                'delivery_status' => 'failed',
                'consumed_at' => now(),
            ])->save();

            $request->session()->forget('customer_otp_phone');

            return back()->withErrors([
                'phone' => 'Kode OTP belum dapat dikirim. Periksa konfigurasi WhatsApp atau coba lagi beberapa saat.',
            ])->withInput(['phone' => $validated['phone']]);
        }

        $otp->forceFill([
            'delivery_status' => 'sent',
            'sent_at' => now(),
        ])->save();

        $request->session()->put('customer_otp_phone', $phone);

        return redirect()
            ->route('customer.otp.form')
            ->with('status', 'Kode verifikasi dikirim ke WhatsApp kamu.');
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('customer_otp_phone')) {
            return redirect()->route('customer.login');
        }

        return view('customer.auth.verify');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $phone = $request->session()->get('customer_otp_phone');

        if (! is_string($phone) || $phone === '') {
            return redirect()->route('customer.login')
                ->withErrors(['phone' => 'Sesi verifikasi berakhir. Silakan minta kode baru.']);
        }

        $verifyKey = 'customer-otp-verify:'.hash('sha256', $phone.'|'.(string) $request->ip());

        if (RateLimiter::tooManyAttempts($verifyKey, 10)) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
            ]);
        }

        RateLimiter::hit($verifyKey, 900);

        $result = DB::transaction(function () use ($phone, $validated) {
            $otp = CustomerOtp::query()
                ->where('phone', $phone)
                ->where('delivery_status', 'sent')
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $otp) {
                return ['status' => 'invalid'];
            }

            if ($otp->expires_at->isPast()) {
                $otp->forceFill(['consumed_at' => now()])->save();

                return ['status' => 'expired'];
            }

            if ($otp->attempts >= self::MAX_OTP_ATTEMPTS) {
                $otp->forceFill(['consumed_at' => now()])->save();

                return ['status' => 'locked'];
            }

            $otp->increment('attempts');

            if (! Hash::check($validated['otp'], $otp->code_hash)) {
                if ($otp->fresh()->attempts >= self::MAX_OTP_ATTEMPTS) {
                    $otp->forceFill(['consumed_at' => now()])->save();
                }

                return ['status' => 'mismatch'];
            }

            $otp->forceFill(['consumed_at' => now()])->save();

            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                ['name' => null]
            );

            $customer->forceFill(['phone_verified_at' => now()])->save();

            return ['status' => 'verified', 'customer' => $customer];
        });

        if ($result['status'] !== 'verified') {
            $message = match ($result['status']) {
                'expired' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
                'locked' => 'Batas percobaan kode sudah tercapai. Silakan minta kode baru.',
                'mismatch' => 'Kode OTP salah.',
                default => 'Kode OTP tidak valid. Silakan minta kode baru.',
            };

            return back()->withErrors(['otp' => $message]);
        }

        RateLimiter::clear($verifyKey);
        Auth::guard('customer')->login($result['customer']);
        $request->session()->regenerate();
        $request->session()->forget('customer_otp_phone');

        return redirect()->intended(route('customer.dashboard'));
    }

    public function dashboard(): View
    {
        $customer = Auth::guard('customer')->user();
        $customer->load('addresses');

        return view('customer.dashboard', [
            'customer' => $customer,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->regenerate();

        return redirect()->route('customer.login');
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
