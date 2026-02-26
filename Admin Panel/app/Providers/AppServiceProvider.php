<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\VendorRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\VendorRepository;
use App\Services\AppointmentService;
use App\Services\CartCheckoutService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\ReturnRefundService;
use App\Services\StockService;
use App\Services\WalletService;
use App\Services\ZoneService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ── Repository bindings ────────────────────────────────────────────────
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(OrderRepositoryInterface::class,  OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // ── Service bindings (singletons) ─────────────────────────────────────
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(ZoneService::class);
        $this->app->singleton(WalletService::class);
        $this->app->singleton(StockService::class);
        $this->app->singleton(AppointmentService::class);
        $this->app->singleton(ReturnRefundService::class);

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(WalletService::class),
                $app->make(NotificationService::class),
            );
        });

        $this->app->singleton(CartCheckoutService::class, function ($app) {
            return new CartCheckoutService(
                $app->make(StockService::class),
            );
        });

        // ── Countries data shared with all views ───────────────────────────────
        $countriesData = [];
        $jsonPath = public_path('countriesdata.json');
        if (file_exists($jsonPath)) {
            $json = file_get_contents($jsonPath);
            if ($json) {
                $countriesData = json_decode($json);
            }
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
