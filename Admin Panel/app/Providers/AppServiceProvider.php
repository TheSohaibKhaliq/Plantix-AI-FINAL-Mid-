<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\VendorRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\VendorRepository;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\WalletService;
use App\Services\ZoneService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // -----------------------------------------------------------------------
        // Repository bindings
        // -----------------------------------------------------------------------
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // -----------------------------------------------------------------------
        // Service bindings (singletons where state is safe to share per request)
        // -----------------------------------------------------------------------
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(ZoneService::class);

        $this->app->singleton(WalletService::class);
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(WalletService::class),
                $app->make(NotificationService::class),
            );
        });

        // -----------------------------------------------------------------------
        // Countries data shared with all views (existing behaviour preserved)
        // -----------------------------------------------------------------------
        $countriesData = [];
        $json = file_get_contents(public_path('countriesdata.json'));
        if ($json) {
            $countriesData = json_decode($json);
        }
        view()->composer('*', function ($view) use ($countriesData) {
            $view->with('countries_data', $countriesData);
        });
    }

    public function boot(): void
    {
        //
    }
}
