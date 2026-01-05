<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security headers not handled by Spatie CSP.
 *
 * Spatie CSP handles: Content-Security-Policy
 * This middleware handles: HSTS, X-Frame-Options, X-Content-Type-Options, etc.
 */
class SecurityHeaders
{
    /**
     * Security headers configuration.
     * CSP is handled by Spatie\Csp package.
     */
    private const array HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=()',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apply security headers
        foreach (self::HEADERS as $header => $value) {
            $response->headers->set($header, $value);
        }

        // HSTS only on HTTPS
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }
}
