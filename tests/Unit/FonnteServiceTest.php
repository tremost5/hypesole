<?php

namespace Tests\Unit;

use App\Services\FonnteService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteServiceTest extends TestCase
{
    public function test_it_accepts_an_explicit_success_response_from_fonnte(): void
    {
        config([
            'services.fonnte.token' => 'test-token',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
        ]);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $sent = app(FonnteService::class)->sendOtp('6281234567890', '123456');

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'test-token')
                && $request['target'] === '6281234567890'
                && str_contains($request['message'], '123456');
        });
    }

    public function test_it_does_not_treat_a_non_success_provider_response_as_sent(): void
    {
        config([
            'services.fonnte.token' => 'test-token',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
        ]);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false], 200),
        ]);

        $this->assertFalse(
            app(FonnteService::class)->sendOtp('6281234567890', '123456')
        );
    }

    public function test_it_fails_safely_when_fonnte_is_not_configured(): void
    {
        config([
            'services.fonnte.token' => '',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
        ]);

        Http::fake();

        $this->assertFalse(
            app(FonnteService::class)->sendOtp('6281234567890', '123456')
        );

        Http::assertNothingSent();
    }
}
