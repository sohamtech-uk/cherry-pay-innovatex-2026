<?php

namespace App\Providers;

use App\Services\CherryPay\Settlement\DemoSettlementVerifier;
use App\Services\CherryPay\Settlement\EvmSettlementVerifier;
use App\Services\CherryPay\Settlement\SettlementVerifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SettlementVerifier::class, function ($app) {
            return config('cherry-pay.demo_mode')
                ? $app->make(DemoSettlementVerifier::class)
                : $app->make(EvmSettlementVerifier::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
