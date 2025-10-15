<?php

namespace Rastik1584\LaravelMapyczApi;

use Illuminate\Support\ServiceProvider;

class MapyczApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/mapycz-api.php', 'mapycz-api'
        );

        // Register the main class to use with the facade
        $this->app->singleton('mapycz-api', function ($app) {
            return new \Rastik1584\LaravelMapyczApi\MapyczApi(
                $app->make(\Rastik1584\LaravelMapyczApi\MapyczApiClient::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/mapycz-api.php' => config_path('mapycz-api.php'),
        ], 'config');

        // Register helper
        if (file_exists(__DIR__.'/helpers.php')) {
            require_once __DIR__.'/helpers.php';
        }
    }
}