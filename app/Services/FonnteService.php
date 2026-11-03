<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    /**
     * Send an OTP through Fonnte. A successful HTTP response alone is not
     * treated as success; Fonnte's JSON status must explicitly be true.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $token = (string) config('services.fonnte.token');
        $endpoint = (string) config('services.fonnte.endpoint');
        try {
            $storedToken = \App\Models\SiteSetting::where('key', 'fonnte_token')->value('value');
            if ($storedToken) $token = \Illuminate\Support\Facades\Crypt::decryptString($storedToken);
            $endpoint = (string) (\App\Models\SiteSetting::where('key', 'fonnte_endpoint')->value('value') ?: $endpoint);
        } catch (Throwable $exception) { Log::warning('Unable to load encrypted Fonnte settings.'); }
        try {
            $storedToken = \App\Models\SiteSetting::where('key', 'fonnte_token')->value('value');
            if ($storedToken) $token = \Illuminate\Support\Facades\Crypt::decryptString($storedToken);
            $endpoint = (string) (\App\Models\SiteSetting::where('key', 'fonnte_endpoint')->value('value') ?: $endpoint);
        } catch (Throwable $exception) { Log::warning('Unable to load encrypted Fonnte settings.'); }

        if ($token === '' || $endpoint === '') {
            Log::warning('Fonnte OTP is not configured.');
            return false;
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post($endpoint, [
                    'target' => $phone,
                    'message' => "Kode OTP HYPESOLE kamu: {$code}. Berlaku 5 menit. Jangan bagikan kode ini kepada siapa pun.",
                ]);

            $accepted = $response->successful()
                && $response->json('status') === true;

            if (! $accepted) {
                Log::warning('Fonnte rejected an OTP request.', [
                    'http_status' => $response->status(),
                ]);
            }

            return $accepted;
        } catch (Throwable $exception) {
            // Do not log the exception message: HTTP exceptions may contain request details.
            Log::warning('Fonnte OTP request failed.', [
                'exception_class' => get_class($exception),
            ]);

            return false;
        }
    }
}
