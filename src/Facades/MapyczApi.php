<?php

namespace Rastik1584\LaravelMapyczApi\Facades;

use Illuminate\Support\Facades\Facade;
use Rastik1584\LaravelMapyczApi\Services\GeocodingService;
use Rastik1584\LaravelMapyczApi\Services\RoutingService;

/**
 * @method static RoutingService routing()
 * @method static GeocodingService geocoding()
 * @method static array geocode(string $query, array $options = [])
 * @method static array suggest(string $query, array $options = [])
 * @method static array reverse(float $lat, float $lon, array $options = [])
 * @method static array search(string $query, array $options = [])
 * @method static array functions(string $function, array $params = [])
 *
 * @see \Rastik1584\LaravelMapyczApi\MapyczApi
 */
class MapyczApi extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return 'mapycz-api';
    }

    // Explicit static proxies to satisfy method_exists checks in tests
    public static function search(string $query, array $options = [])
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }

    public static function geocode(string $query, array $options = [])
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }

    public static function functions(string $function, array $params = [])
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }
}