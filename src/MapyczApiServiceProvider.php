<?php

namespace Rastik1584\LaravelMapyczApi;

use Illuminate\Support\ServiceProvider;
use Rastik1584\LaravelMapyczApi\Services\ElevationService;
use Rastik1584\LaravelMapyczApi\Services\GeocodingService;
use Rastik1584\LaravelMapyczApi\Services\RoutingService;
use Rastik1584\LaravelMapyczApi\Services\StaticMapService;
use Rastik1584\LaravelMapyczApi\Services\TileMapService;
use Rastik1584\LaravelMapyczApi\Services\TimezoneService;

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

        $this->app->singleton(MapyczApiClient::class, fn () => new MapyczApiClient);

        $this->app->singleton(GeocodingService::class, fn ($app) => new GeocodingService($app->make(MapyczApiClient::class)));
        $this->app->singleton(RoutingService::class, fn ($app) => new RoutingService($app->make(MapyczApiClient::class)));
        $this->app->singleton(StaticMapService::class, fn ($app) => new StaticMapService($app->make(MapyczApiClient::class)));
        $this->app->singleton(TileMapService::class, fn ($app) => new TileMapService($app->make(MapyczApiClient::class)));
        $this->app->singleton(ElevationService::class, fn ($app) => new ElevationService($app->make(MapyczApiClient::class)));
        $this->app->singleton(TimezoneService::class, fn ($app) => new TimezoneService($app->make(MapyczApiClient::class)));

        $this->app->singleton(MapyczApi::class, function ($app) {
            return new MapyczApi($app->make(MapyczApiClient::class));
        });

        $this->app->alias(MapyczApi::class, 'mapycz-api');
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
