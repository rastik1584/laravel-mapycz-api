<?php

namespace Rastik1584\LaravelMapyczApi\Facades;

use Illuminate\Support\Facades\Facade;
use Rastik1584\LaravelMapyczApi\DTO\StaticMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticPanoramaParams;
use Rastik1584\LaravelMapyczApi\Services\ElevationService;
use Rastik1584\LaravelMapyczApi\Services\GeocodingService;
use Rastik1584\LaravelMapyczApi\Services\RoutingService;
use Rastik1584\LaravelMapyczApi\Services\StaticMapService;
use Rastik1584\LaravelMapyczApi\Services\TileMapService;
use Rastik1584\LaravelMapyczApi\Services\TimezoneService;

/**
 * @method static RoutingService routing()
 * @method static GeocodingService geocoding()
 * @method static StaticMapService staticMaps()
 * @method static TileMapService tileMaps()
 * @method static ElevationService elevation()
 * @method static TimezoneService timezone()
 * @method static array geocode(string $query, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\GeocodeResult geocodeResult(string $query, array $options = [])
 * @method static array suggest(string $query, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\GeocodeResult suggestResult(string $query, array $options = [])
 * @method static array reverse(float $lat, float $lon, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\GeocodeResult reverseResult(float $lat, float $lon, array $options = [])
 * @method static string staticImage(string $storagePath, \Rastik1584\LaravelMapyczApi\DTO\StaticMapParams|array $params, string $filename = '', string $disk = 'public')
 * @method static string panoramaImage(string $storagePath, \Rastik1584\LaravelMapyczApi\DTO\StaticPanoramaParams|array $params, string $filename = '', string $disk = 'public')
 * @method static string tile(string $mapset, int $zoom, int $x, int $y, string $path, string $tileSize = '256', string $filename = '', string $disk = 'public', ?string $lang = null)
 * @method static array tileJson(string $mapset, ?string $lang = null)
 * @method static \Rastik1584\LaravelMapyczApi\Responses\TileJsonResult tileJsonResult(string $mapset, ?string $lang = null)
 * @method static array route(array|\Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams $params)
 * @method static array matrixM(array|\Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams $params)
 * @method static array elevations(array|\Rastik1584\LaravelMapyczApi\DTO\ElevationParams $params, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\ElevationResult elevationsResult(array|\Rastik1584\LaravelMapyczApi\DTO\ElevationParams $params, array $options = [])
 * @method static array listTimezones(array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneListParams $params = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\TimezoneListResult listTimezonesResult(array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneListParams $params = [])
 * @method static array timezoneInfo(string|array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneNameParams $params, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\TimezoneInfoResult timezoneInfoResult(string|array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneNameParams $params, array $options = [])
 * @method static array timezoneByCoordinates(float|array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\TimezoneInfoResult timezoneByCoordinatesResult(float|array|\Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = [])
 * @method static array search(string $query, array $options = [])
 * @method static \Rastik1584\LaravelMapyczApi\Responses\RoutingResult routeResult(array|\Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams $params)
 * @method static \Rastik1584\LaravelMapyczApi\Responses\MatrixResult matrixResult(array|\Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams $params)
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
        return \Rastik1584\LaravelMapyczApi\MapyczApi::class;
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

    public static function staticImage(string $storagePath, StaticMapParams|array $params, string $filename = '', string $disk = 'public')
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }

    public static function panoramaImage(string $storagePath, StaticPanoramaParams|array $params, string $filename = '', string $disk = 'public')
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }

    public static function tile(string $mapset, int $zoom, int $x, int $y, string $path, string $tileSize = '256', string $filename = '', string $disk = 'public', ?string $lang = null)
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }

    public static function tileJson(string $mapset, ?string $lang = null)
    {
        return parent::__callStatic(__FUNCTION__, func_get_args());
    }
}
