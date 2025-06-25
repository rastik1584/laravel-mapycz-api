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
            return new MapyczApi(config('mapycz-api'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/mapycz-api.php' => config_path('mapycz-api.php'),
        ], 'config');
    }
}