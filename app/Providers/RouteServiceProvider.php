<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    #[\Override]
    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        // Per-email soft lockout: 10 attempts per 15 minutes, regardless of IP.
        // Blocks credential stuffing with IP rotation without locking out legitimate users permanently.
        // SHA-256 hash avoids storing PII directly in Redis keys.
        RateLimiter::for(
            'login-by-email',
            fn (Request $request) => Limit::perMinutes(15, 10)->by('email:' . hash('sha256', $request->str('email')->lower()->value()))
        );

        $this->routes(function() {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
