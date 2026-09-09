<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Tests\TestCase;

class MaintenancePageTest extends TestCase
{
    public function test_web_503_uses_branded_maintenance_page_even_when_debug_is_enabled(): void
    {
        config([
            'app.debug' => true,
            'app.key' => 'base64:'.base64_encode(str_repeat('m', 32)),
        ]);
        $response = app(ExceptionHandler::class)->render(
            Request::create('/super-admin/telegram-bots'),
            new ServiceUnavailableHttpException,
        );

        TestResponse::fromBaseResponse($response)
            ->assertStatus(503)
            ->assertHeader('Retry-After', '30')
            ->assertSee('Kami sedang membuat SISFO')
            ->assertSee('SMK Telkom Lampung')
            ->assertDontSee('Symfony\\Component\\HttpKernel\\Exception');
    }
}
