<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaPublicAssetsTest extends TestCase
{
    public function test_manifest_is_public_json_with_hypesole_identity(): void
    {
        $response = $this->get('/manifest.webmanifest');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8');

        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('HYPESOLE', $manifest['name']);
        $this->assertSame('HYPESOLE', $manifest['short_name']);
        $this->assertSame('./', $manifest['start_url']);
        $this->assertSame('./', $manifest['scope']);
    }

    public function test_service_worker_allows_the_configured_subfolder_scope(): void
    {
        config(['app.url' => 'https://malang-web.site/hypesole']);

        $response = $this->get('/service-worker.js');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->assertHeader('Service-Worker-Allowed', '/hypesole/');

        $this->assertStringContainsString('self.addEventListener', file_get_contents(public_path('service-worker.js')));
    }

    public function test_offline_page_and_allowed_icons_are_public(): void
    {
        $this->get('/offline.html')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=utf-8');
        $this->assertStringContainsString('HYPESOLE', file_get_contents(public_path('offline.html')));

        foreach (['icon-192.png', 'icon-512.png', 'icon-512-maskable.png'] as $icon) {
            $this->get('/icons/'.$icon)
                ->assertOk()
                ->assertHeader('Content-Type', 'image/png');
        }

        $this->get('/icons/not-an-app-icon.png')->assertNotFound();
    }
}
