<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class SecurityHeadersTest extends TestCase
{
    private SecurityHeaders $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SecurityHeaders;
    }

    public function test_applies_security_headers(): void
    {
        $request  = Request::create('/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertEquals('nosniff', $result->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $result->headers->get('X-Frame-Options'));
        $this->assertEquals('1; mode=block', $result->headers->get('X-XSS-Protection'));
        $this->assertEquals('strict-origin-when-cross-origin', $result->headers->get('Referrer-Policy'));
        $this->assertStringContainsString('geolocation=()', $result->headers->get('Permissions-Policy'));
    }

    public function test_applies_hsts_header_on_https(): void
    {
        $request  = Request::create('https://example.com/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $hsts = $result->headers->get('Strict-Transport-Security');
        $this->assertNotNull($hsts);
        $this->assertStringContainsString('max-age=31536000', $hsts);
        $this->assertStringContainsString('includeSubDomains', $hsts);
        $this->assertStringContainsString('preload', $hsts);
    }

    public function test_does_not_apply_hsts_header_on_http(): void
    {
        $request  = Request::create('http://example.com/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertNull($result->headers->get('Strict-Transport-Security'));
    }
}
