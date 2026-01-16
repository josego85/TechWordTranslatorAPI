<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\JsonResponse;
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

    public function test_applies_all_security_headers_to_http_response(): void
    {
        $request  = Request::create('/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        // Verify all required headers
        $this->assertEquals('nosniff', $result->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $result->headers->get('X-Frame-Options'));
        $this->assertEquals('1; mode=block', $result->headers->get('X-XSS-Protection'));
        $this->assertEquals('strict-origin-when-cross-origin', $result->headers->get('Referrer-Policy'));

        $permissionsPolicy = $result->headers->get('Permissions-Policy');
        $this->assertStringContainsString('geolocation=()', $permissionsPolicy);
        $this->assertStringContainsString('microphone=()', $permissionsPolicy);
        $this->assertStringContainsString('camera=()', $permissionsPolicy);
        $this->assertStringContainsString('payment=()', $permissionsPolicy);
        $this->assertStringContainsString('usb=()', $permissionsPolicy);

        // HSTS should NOT be present on HTTP
        $this->assertNull($result->headers->get('Strict-Transport-Security'));
    }

    public function test_applies_hsts_header_on_https_requests(): void
    {
        $request  = Request::create('https://example.com/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $hsts = $result->headers->get('Strict-Transport-Security');
        $this->assertNotNull($hsts);
        $this->assertEquals('max-age=31536000; includeSubDomains; preload', $hsts);

        // Verify other headers are still applied
        $this->assertEquals('nosniff', $result->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $result->headers->get('X-Frame-Options'));
    }

    public function test_does_not_apply_hsts_header_on_http_requests(): void
    {
        $request  = Request::create('http://example.com/test', 'GET');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertNull($result->headers->get('Strict-Transport-Security'));
    }

    public function test_applies_headers_to_json_responses(): void
    {
        $request  = Request::create('/api/test', 'GET');
        $response = new JsonResponse(['status' => 'ok']);

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertEquals('nosniff', $result->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $result->headers->get('X-Frame-Options'));
        $this->assertEquals('strict-origin-when-cross-origin', $result->headers->get('Referrer-Policy'));
    }

    public function test_preserves_existing_response_content(): void
    {
        $request  = Request::create('/test', 'GET');
        $response = new Response('Test Content', 200);

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertEquals('Test Content', $result->getContent());
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_middleware_handles_post_requests(): void
    {
        $request  = Request::create('/test', 'POST');
        $response = new Response;

        $result = $this->middleware->handle($request, fn () => $response);

        $this->assertEquals('nosniff', $result->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $result->headers->get('X-Frame-Options'));
    }
}
