<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi;

use Rastik1584\LaravelMapyczApi\DTO\ElevationParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticPanoramaParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneCoordinateParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneListParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneNameParams;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\Responses\ElevationResult;
use Rastik1584\LaravelMapyczApi\Responses\GeocodeResult;
use Rastik1584\LaravelMapyczApi\Responses\MatrixResult;
use Rastik1584\LaravelMapyczApi\Responses\RoutingResult;
use Rastik1584\LaravelMapyczApi\Responses\TileJsonResult;
use Rastik1584\LaravelMapyczApi\Responses\TimezoneInfoResult;
use Rastik1584\LaravelMapyczApi\Responses\TimezoneListResult;
use Rastik1584\LaravelMapyczApi\Services\ElevationService;
use Rastik1584\LaravelMapyczApi\Services\GeocodingService;
use Rastik1584\LaravelMapyczApi\Services\RoutingService;
use Rastik1584\LaravelMapyczApi\Services\StaticMapService;
use Rastik1584\LaravelMapyczApi\Services\TileMapService;
use Rastik1584\LaravelMapyczApi\Services\TimezoneService;

class MapyczApi
{
    public function __construct(
        protected MapyczApiClient $client
    ) {}

    public function routing(): RoutingService
    {
        return new RoutingService($this->client);
    }

    public function geocoding(): GeocodingService
    {
        return new GeocodingService($this->client);
    }

    public function staticMaps(): StaticMapService
    {
        return new StaticMapService($this->client);
    }

    public function tileMaps(): TileMapService
    {
        return new TileMapService($this->client);
    }

    public function elevation(): ElevationService
    {
        return new ElevationService($this->client);
    }

    public function timezone(): TimezoneService
    {
        return new TimezoneService($this->client);
    }

    // Convenience shortcuts
    public function geocode(string $query, array $options = []): array
    {
        return $this->geocoding()->geocode($query, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function geocodeResult(string $query, array $options = []): GeocodeResult
    {
        return $this->geocoding()->geocodeResult($query, $options);
    }

    public function suggest(string $query, array $options = []): array
    {
        return $this->geocoding()->suggest($query, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function suggestResult(string $query, array $options = []): GeocodeResult
    {
        return $this->geocoding()->suggestResult($query, $options);
    }

    public function reverse(float $lat, float $lon, array $options = []): array
    {
        return $this->geocoding()->reverse($lat, $lon, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function reverseResult(float $lat, float $lon, array $options = []): GeocodeResult
    {
        return $this->geocoding()->reverseResult($lat, $lon, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function staticImage(string $storagePath, StaticMapParams|array $params, string $filename = '', string $disk = 'public'): string
    {
        return $this->staticMaps()->image($storagePath, $params, $filename, $disk);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function panoramaImage(string $storagePath, StaticPanoramaParams|array $params, string $filename = '', string $disk = 'public'): string
    {
        return $this->staticMaps()->panorama($storagePath, $params, $filename, $disk);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function tile(string $mapset, int $zoom, int $x, int $y, string $path, string $tileSize = '256', string $filename = '', string $disk = 'public', ?string $lang = null): string
    {
        return $this->tileMaps()->tile($mapset, $zoom, $x, $y, $path, $tileSize, $filename, $disk, $lang);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function tileJson(string $mapset, ?string $lang = null): array
    {
        return $this->tileMaps()->tileJson($mapset, $lang);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function tileJsonResult(string $mapset, ?string $lang = null): TileJsonResult
    {
        return $this->tileMaps()->tileJsonResult($mapset, $lang);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function route(array|RouteMapParams $params): array
    {
        return $this->routing()->route($params);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function matrixM(array|MatrixMapParams $params): array
    {
        return $this->routing()->matrixM($params);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function elevations(array|ElevationParams $params, array $options = []): array
    {
        return $this->elevation()->elevations($params, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function elevationsResult(array|ElevationParams $params, array $options = []): ElevationResult
    {
        return $this->elevation()->elevationsResult($params, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function listTimezones(array|TimezoneListParams $params = []): array
    {
        return $this->timezone()->list($params);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function listTimezonesResult(array|TimezoneListParams $params = []): TimezoneListResult
    {
        return $this->timezone()->listResult($params);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezoneInfo(string|array|TimezoneNameParams $params, array $options = []): array
    {
        return $this->timezone()->timezone($params, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezoneInfoResult(string|array|TimezoneNameParams $params, array $options = []): TimezoneInfoResult
    {
        return $this->timezone()->timezoneResult($params, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezoneByCoordinates(float|array|TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = []): array
    {
        return $this->timezone()->coordinate($lonOrParams, $lat, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezoneByCoordinatesResult(float|array|TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = []): TimezoneInfoResult
    {
        return $this->timezone()->coordinateResult($lonOrParams, $lat, $options);
    }

    /**
     * Backward-compatible generic search shortcut -> maps to suggest endpoint.
     *
     * @throws MapyczApiRequestException
     */
    public function search(string $query, array $options = []): array
    {
        return $this->suggest($query, $options);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function routeResult(array|RouteMapParams $params): RoutingResult
    {
        return $this->routing()->routeResult($params);
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function matrixResult(array|MatrixMapParams $params): MatrixResult
    {
        return $this->routing()->matrixResult($params);
    }
}
