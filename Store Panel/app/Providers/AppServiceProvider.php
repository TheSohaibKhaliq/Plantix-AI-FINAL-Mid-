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
        // Repository bindings
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // Service singletons
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(WalletService::class);
        $this->app->singleton(ZoneService::class);
        $this->app->singleton(OrderService::class);
    }

    public function boot(): void
    {
        //
    }
}
