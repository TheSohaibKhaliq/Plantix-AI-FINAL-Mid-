<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /*
    |--------------------------------------------------------------------------
    | Panel HOME paths — used by RedirectIfAuthenticated middleware
    |--------------------------------------------------------------------------
    | Each guard redirects to its own dashboard after a successful login.
    | The "web" guard HOME is used as the fallback for the base Laravel
    | scaffolding (e.g. password-reset redirects).
    */

    /** @var string  Fallback for web/customer guard */
    public const HOME = '/';

    /** @var string  Admin panel home */
    public const ADMIN_HOME   = '/admin/dashboard';

    /** @var string  Vendor/Store panel home */
    public const VENDOR_HOME  = '/vendor/dashboard';

    /** @var string  Expert panel home */
    public const EXPERT_HOME  = '/expert/dashboard';

    /**
     * Define your route model bindings, pattern filters, and rate limits.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // ── REST API ────────────────────────────────────────────────────
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // ── Web (all panels) — delegated to routes/panels/*.php ─────────
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Separate buckets let each panel be throttled independently.
     */
    protected function configureRateLimiting(): void
    {
        // Default API bucket
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });

        // Admin-panel API calls (higher limit – server-side, not public)
        RateLimiter::for('admin-api', function (Request $request) {
            return Limit::perMinute(200)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });

        // Expert panel API calls
        RateLimiter::for('expert-api', function (Request $request) {
            return Limit::perMinute(120)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });
    }
}
