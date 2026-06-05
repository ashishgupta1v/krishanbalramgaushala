<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \App\Infrastructure\Gateways\WhatsAppGateway::class,
            function () {
                if (env('META_WA_ACCESS_TOKEN')) {
                    return new \App\Infrastructure\Gateways\MetaWhatsAppGateway();
                }
                return new \App\Infrastructure\Gateways\MockWhatsAppGateway();
            }
        );

        $this->app->singleton(
            \App\Infrastructure\Gateways\FacebookGateway::class,
            function () {
                if (env('FB_PAGE_ACCESS_TOKEN')) {
                    return new \App\Infrastructure\Gateways\MetaFacebookGateway();
                }
                return new \App\Infrastructure\Gateways\MockFacebookGateway();
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
