<?php

namespace App\Providers;

use App\Services\MerchantManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
        $merchant = new MerchantManager;

        $this->app->instance(MerchantManager::class, $merchant);
        $this->app->bind(Merchant::class, function () use ($merchant) {
            return $merchant->getTenant();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginator::useBootstrap();
    }
}
