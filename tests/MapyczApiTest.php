<?php

namespace Rastik1584\LaravelMapyczApi\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use Rastik1584\LaravelMapyczApi\DTO\ElevationParams;
use Rastik1584\LaravelMapyczApi\DTO\Geocoding\GeocodeParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticPanoramaParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneCoordinateParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneListParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneNameParams;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\Exceptions\MissingApiKeyException;
use Rastik1584\LaravelMapyczApi\Facades\MapyczApi as MapyczApiFacade;
use Rastik1584\LaravelMapyczApi\MapyczApi;
use Rastik1584\LaravelMapyczApi\MapyczApiServiceProvider;
use Rastik1584\LaravelMapyczApi\Services\ElevationService;
use Rastik1584\LaravelMapyczApi\Services\StaticMapService;
use Rastik1584\LaravelMapyczApi\Services\TileMapService;
use Rastik1584\LaravelMapyczApi\Services\TimezoneService;

class MapyczApiTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MapyczApiServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'MapyczApi' => MapyczApiFacade::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('mapycz-api.api_key', 'test-api-key');
        $app['config']->set('mapycz-api.cache.enabled', false);
        $app['config']->set('mapycz-api.cache.ttl', 3600);
        $app['config']->set('filesystems.default', 'public');
    }

    public function test_it_registers_public_services_in_the_container()
    {
        $this->assertInstanceOf(MapyczApi::class, $this->app->make(MapyczApi::class));
        $this->assertInstanceOf(StaticMapService::class, $this->app->make(StaticMapService::class));
        $this->assertInstanceOf(TileMapService::class, $this->app->make(TileMapService::class));
        $this->assertInstanceOf(ElevationService::class, $this->app->make(ElevationService::class));
        $this->assertInstanceOf(TimezoneService::class, $this->app->make(TimezoneService::class));
    }

    public function test_it_does_not_expose_the_removed_functions_passthrough()
    {
        $this->assertFalse(method_exists(MapyczApi::class, 'functions'));
        $this->assertFalse(method_exists(MapyczApiFacade::class, 'functions'));
    }

    public function test_it_geocodes_queries_via_the_shared_client()
    {
        Http::fake([
            '*' => Http::response(['items' => []], 200),
        ]);

        $response = $this->app->make(MapyczApi::class)->geocode('Praha', ['limit' => 7]);

        $this->assertSame(['items' => []], $response);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/geocode')
                && $query['apikey'] === 'test-api-key'
                && $query['query'] === 'Praha'
                && $query['limit'] === '7';
        });
    }

    public function test_it_maps_geocode_results_to_typed_response_objects()
    {
        Http::fake([
            '*' => Http::response([
                'items' => [[
                    'name' => 'Praha',
                    'label' => 'Mesto',
                    'position' => ['lon' => 14.4378, 'lat' => 50.0755],
                    'type' => 'regional.municipality',
                    'regionalStructure' => [
                        ['name' => 'Cesko', 'type' => 'regional.country', 'isoCode' => 'CZ'],
                    ],
                ]],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->geocodeResult('Praha');

        $this->assertSame('Praha', $result->items[0]->name);
        $this->assertSame(14.4378, $result->items[0]->position->lon);
        $this->assertSame('CZ', $result->items[0]->regionalStructure[0]->isoCode);
    }

    public function test_it_uses_rgeocode_for_reverse_geocoding()
    {
        Http::fake([
            '*' => Http::response(['items' => []], 200),
        ]);

        $this->app->make(MapyczApi::class)->reverse(50.08861, 14.42212);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/rgeocode')
                && $query['lat'] === '50.08861'
                && $query['lon'] === '14.42212';
        });
    }

    public function test_it_serializes_extended_geocode_filters()
    {
        Http::fake([
            '*' => Http::response(['items' => []], 200),
        ]);

        $params = new GeocodeParams(
            query: 'Praha',
            limit: 5,
            type: ['regional', 'poi'],
            locality: ['Praha', 'cz'],
            preferBBox: [14.2, 49.9, 14.7, 50.2]
        );

        $this->app->make(MapyczApi::class)->geocoding()->geocode($params);

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, '/v1/geocode?')
                && str_contains($url, 'query=Praha')
                && str_contains($url, 'type=regional')
                && str_contains($url, 'type=poi')
                && str_contains($url, 'locality=Praha')
                && str_contains($url, 'locality=cz')
                && str_contains($url, 'preferBBox=14.2%2C49.9%2C14.7%2C50.2');
        });
    }

    public function test_it_serializes_route_dto_parameters_correctly()
    {
        Http::fake([
            '*' => Http::response(['length' => 123], 200),
        ]);

        $dto = new RouteMapParams(
            start: [14.40094, 50.0711],
            end: [16.5990122, 49.1718222],
            routeType: 'car_fast',
            waypoints: [[15.5903861, 49.3967233]],
            avoidToll: true,
            avoidHighways: true
        );

        $response = $this->app->make(MapyczApi::class)->routing()->route($dto);

        $this->assertSame(['length' => 123], $response);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/routing/route')
                && $query['start'] === '14.40094,50.0711'
                && $query['end'] === '16.5990122,49.1718222'
                && $query['routeType'] === 'car_fast'
                && $query['format'] === 'geojson'
                && $query['avoidToll'] === '1'
                && $query['avoidHighways'] === '1'
                && $query['waypoints'] === '15.5903861,49.3967233';
        });
    }

    public function test_it_maps_route_results_to_typed_response_objects()
    {
        Http::fake([
            '*' => Http::response([
                'length' => 195,
                'duration' => 42,
                'geometry' => ['type' => 'Feature'],
                'parts' => [
                    ['length' => 119, 'duration' => 25],
                ],
                'routePoints' => [
                    [
                        'originalPosition' => [14.4372475, 50.1002117],
                        'mappedPosition' => [14.4372292, 50.1002125],
                        'snapDistance' => 1,
                        'restricted' => true,
                    ],
                ],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->routeResult([
            'start' => '14.40094,50.0711',
            'end' => '16.5990122,49.1718222',
            'routeType' => 'car_fast',
        ]);

        $this->assertSame(195, $result->length);
        $this->assertSame(25, $result->parts[0]->duration);
        $this->assertTrue($result->routePoints[0]->restricted);
    }

    public function test_it_serializes_matrix_dto_parameters_correctly()
    {
        Http::fake([
            '*' => Http::response(['matrix' => []], 200),
        ]);

        $dto = new MatrixMapParams(
            starts: [[14.40094, 50.0711], [14.3951303, 50.0704094]],
            ends: [[14.40194, 50.0721]],
            routeType: 'car_fast'
        );

        $response = $this->app->make(MapyczApi::class)->routing()->matrixM($dto);

        $this->assertSame(['matrix' => []], $response);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/routing/matrix-m')
                && $query['starts'] === '14.40094,50.0711;14.3951303,50.0704094'
                && $query['ends'] === '14.40194,50.0721'
                && $query['routeType'] === 'car_fast';
        });
    }

    public function test_it_can_store_a_static_map_image()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('PNGDATA', 200, ['Content-Type' => 'image/png']),
        ]);

        $params = new StaticMapParams(
            width: 400,
            height: 300,
            markers: [[
                'color' => 'red',
                'size' => 'normal',
                'points' => [
                    ['lon' => 14.42212, 'lat' => 50.08861, 'label' => 'A'],
                ],
            ]]
        );

        $filename = $this->app->make(MapyczApi::class)->staticImage('maps', $params, 'map.png');

        $this->assertSame('map.png', $filename);
        $this->assertTrue(Storage::disk('public')->exists('maps/map.png'));

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, '/v1/static/map?')
                && str_contains($url, 'mapset=basic')
                && str_contains($url, 'markers=color%3Ared%3Bsize%3Anormal%3Blabel%3AA%3B14.42212%2C50.08861');
        });
    }

    public function test_it_serializes_structured_static_map_shapes()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('PNGDATA', 200, ['Content-Type' => 'image/png']),
        ]);

        $params = new StaticMapParams(
            width: 400,
            height: 300,
            markers: [[
                'points' => [
                    ['lon' => 14.42212, 'lat' => 50.08861],
                ],
            ]],
            shapes: [
                [
                    'color' => 'green',
                    'width' => 2,
                    'path' => [
                        [14.4196, 50.0869],
                        [14.4197, 50.0867],
                    ],
                ],
                [
                    'color' => 'blue',
                    'fill' => '#82bd51aa',
                    'polygon' => [[
                        [14.4219, 50.0869],
                        [14.4220, 50.0867],
                        [14.4223, 50.0867],
                    ]],
                ],
            ]
        );

        $this->app->make(MapyczApi::class)->staticImage('maps', $params, 'shape-map.png');

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, 'shapes=color%3Agreen%3Bwidth%3A2%3Bpath%3A%5B%2814.4196%2C50.0869%3B14.4197%2C50.0867%29%5D')
                && str_contains($url, 'shapes=color%3Ablue%3Bfill%3A%2382bd51aa%3Bpolygon%3A%5B%2814.4219%2C50.0869%3B14.422%2C50.0867%3B14.4223%2C50.0867%29%5D');
        });
    }

    public function test_it_can_store_a_tile_image()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('TILEDATA', 200, ['Content-Type' => 'image/png']),
        ]);

        $filename = $this->app->make(MapyczApi::class)->tile(
            mapset: 'basic',
            zoom: 6,
            x: 34,
            y: 21,
            path: 'tiles',
            tileSize: '256@2x',
            filename: 'tile.png',
            disk: 'public',
            lang: 'en'
        );

        $this->assertSame('tile.png', $filename);
        $this->assertTrue(Storage::disk('public')->exists('tiles/tile.png'));

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/maptiles/basic/256@2x/6/34/21')
                && $query['lang'] === 'en';
        });
    }

    public function test_it_can_store_a_panorama_image()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('JPGDATA', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $params = new StaticPanoramaParams(
            width: 400,
            height: 225,
            lon: 14.42212,
            lat: 50.08861,
            radius: 50,
            yaw: 'point',
            pitch: -0.066,
            fov: 1.2
        );

        $filename = $this->app->make(MapyczApi::class)->panoramaImage('panorama', $params, 'pano.jpg');

        $this->assertSame('pano.jpg', $filename);
        $this->assertTrue(Storage::disk('public')->exists('panorama/pano.jpg'));

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/static/pano')
                && $query['lon'] === '14.42212'
                && $query['lat'] === '50.08861'
                && $query['yaw'] === 'point';
        });
    }

    public function test_it_can_fetch_tilejson_metadata()
    {
        Http::fake([
            '*' => Http::response([
                'tilejson' => '3.0.0',
                'tiles' => ['https://example.test/{z}/{x}/{y}.png'],
            ], 200),
        ]);

        $response = $this->app->make(MapyczApi::class)->tileJson('basic', 'en');

        $this->assertSame('3.0.0', $response['tilejson']);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/maptiles/basic/tiles.json')
                && $query['lang'] === 'en';
        });
    }

    public function test_it_maps_tilejson_results_to_typed_response_objects()
    {
        Http::fake([
            '*' => Http::response([
                'tilejson' => '3.0.0',
                'name' => 'Basic',
                'tiles' => ['https://example.test/{z}/{x}/{y}.png'],
                'minzoom' => 0,
                'maxzoom' => 20,
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->tileJsonResult('basic');

        $this->assertSame('3.0.0', $result->tilejson);
        $this->assertSame('Basic', $result->name);
        $this->assertSame(20, $result->maxzoom);
    }

    public function test_it_fetches_elevation_results()
    {
        Http::fake([
            '*' => Http::response([
                'items' => [
                    [
                        'elevation' => 198.37,
                        'position' => ['lon' => 14.40094, 'lat' => 50.0711],
                    ],
                ],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->elevationsResult(
            new ElevationParams(
                positions: [[14.40094, 50.0711]],
                lang: 'en'
            )
        );

        $this->assertSame(198.37, $result->items[0]->elevation);
        $this->assertSame(14.40094, $result->items[0]->position->lon);

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, '/v1/elevation?')
                && str_contains($url, 'positions=14.40094%2C50.0711')
                && str_contains($url, 'lang=en');
        });
    }

    public function test_it_fetches_timezone_list()
    {
        Http::fake([
            '*' => Http::response([
                'timezones' => ['Europe/Prague', 'Europe/Bratislava'],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->listTimezonesResult(new TimezoneListParams('en'));

        $this->assertSame('Europe/Prague', $result->timezones[0]);

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_ends_with((string) parse_url($request->url(), PHP_URL_PATH), '/v1/timezone/list-timezones')
                && $query['lang'] === 'en';
        });
    }

    public function test_it_fetches_timezone_info_by_name()
    {
        Http::fake([
            '*' => Http::response([
                'timezone' => [
                    'timezoneName' => 'Europe/Prague',
                    'currentTimeAbbreviation' => 'CEST',
                    'standardTimeAbbreviation' => 'CET',
                    'currentLocalTime' => '2024-10-16T15:03:51.248',
                    'currentUtcTime' => '2024-10-16T13:03:51.248Z',
                    'currentUtcOffsetSeconds' => 7200,
                    'standardUtcOffsetSeconds' => 3600,
                    'hasDst' => true,
                    'isDstActive' => true,
                ],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->timezoneInfoResult(
            new TimezoneNameParams('Europe/Prague', 'en')
        );

        $this->assertSame('Europe/Prague', $result->timezone->timezoneName);
        $this->assertTrue($result->timezone->hasDst);

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, '/v1/timezone/timezone?')
                && str_contains($url, 'timezone=Europe%2FPrague')
                && str_contains($url, 'lang=en');
        });
    }

    public function test_it_fetches_timezone_info_by_coordinates()
    {
        Http::fake([
            '*' => Http::response([
                'timezone' => [
                    'timezoneName' => 'Europe/Prague',
                    'currentTimeAbbreviation' => 'CEST',
                    'standardTimeAbbreviation' => 'CET',
                    'currentLocalTime' => '2024-10-16T15:03:51.248',
                    'currentUtcTime' => '2024-10-16T13:03:51.248Z',
                    'currentUtcOffsetSeconds' => 7200,
                    'standardUtcOffsetSeconds' => 3600,
                    'hasDst' => true,
                    'isDstActive' => true,
                    'dstInfo' => [
                        'dstAbbreviation' => 'CEST',
                        'dstStartUtcTime' => '2024-03-31T01:00:00.000Z',
                        'dstStartLocalTime' => '2024-03-31T03:00:00.000',
                        'dstEndUtcTime' => '2024-10-27T01:00:00.000Z',
                        'dstEndLocalTime' => '2024-10-27T02:00:00.000',
                        'dstOffsetSeconds' => 3600,
                        'dstDurationSeconds' => 18144000,
                    ],
                ],
            ], 200),
        ]);

        $result = $this->app->make(MapyczApi::class)->timezoneByCoordinatesResult(
            new TimezoneCoordinateParams(14.42076, 50.08804)
        );

        $this->assertSame('Europe/Prague', $result->timezone->timezoneName);
        $this->assertSame('CEST', $result->timezone->dstInfo?->dstAbbreviation);

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            return str_contains($url, '/v1/timezone/coordinate?')
                && str_contains($url, 'lon=14.42076')
                && str_contains($url, 'lat=50.08804');
        });
    }

    public function test_it_uses_cache_when_enabled_for_identical_requests()
    {
        Cache::flush();
        $this->app['config']->set('mapycz-api.cache.enabled', true);

        Http::fake([
            '*' => Http::response(['items' => [['name' => 'Praha']]], 200),
        ]);

        $api = $this->app->make(MapyczApi::class);

        $first = $api->geocode('Praha');
        $second = $api->geocode('Praha');

        $this->assertSame($first, $second);
        Http::assertSentCount(1);
    }

    public function test_it_throws_a_dedicated_exception_when_api_key_is_missing()
    {
        $this->app['config']->set('mapycz-api.api_key', '');

        $this->expectException(MissingApiKeyException::class);

        $this->app->make(MapyczApi::class)->geocode('Praha');
    }

    public function test_it_throws_a_dedicated_exception_for_invalid_dto_payloads()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        new StaticPanoramaParams(radius: 150);
    }

    public function test_it_wraps_remote_http_failures_in_a_package_exception()
    {
        Http::fake([
            '*' => Http::response([
                'detail' => [
                    ['msg' => 'Edge not found', 'errorCode' => 7],
                ],
            ], 404),
        ]);

        try {
            $this->app->make(MapyczApi::class)->geocode('Praha');
            $this->fail('Expected MapyczApiRequestException was not thrown.');
        } catch (MapyczApiRequestException $exception) {
            $this->assertSame(404, $exception->statusCode);
            $this->assertSame(7, $exception->firstErrorCode());
            $this->assertSame('Edge not found', $exception->firstErrorMessage());
        }
    }

    public function test_it_rejects_conflicting_geocode_bbox_and_near_filters()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        new GeocodeParams(
            query: 'Praha',
            preferBBox: [14.2, 49.9, 14.7, 50.2],
            preferNear: [14.4, 50.1]
        );
    }

    public function test_it_rejects_more_than_fifteen_waypoints()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        new RouteMapParams(
            start: [14.4, 50.07],
            end: [16.59, 49.17],
            routeType: 'car_fast',
            waypoints: array_fill(0, 16, [15.0, 50.0])
        );
    }

    public function test_it_rejects_matrix_requests_larger_than_one_hundred_combinations()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        new MatrixMapParams(
            starts: array_fill(0, 11, [14.4, 50.07]),
            routeType: 'car_fast'
        );
    }

    public function test_it_rejects_unsupported_tile_sizes()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        $this->app->make(MapyczApi::class)->tile(
            mapset: 'basic',
            zoom: 6,
            x: 1,
            y: 1,
            path: 'tiles',
            tileSize: '512'
        );
    }

    public function test_it_rejects_invalid_static_shape_definitions()
    {
        $this->expectException(InvalidMapyczParamsException::class);

        new StaticMapParams(
            width: 400,
            height: 300,
            markers: [[
                'points' => [
                    ['lon' => 14.42212, 'lat' => 50.08861],
                ],
            ]],
            shapes: [
                [
                    'color' => 'green',
                    'path' => [
                        [14.4196, 50.0869],
                    ],
                ],
            ]
        );
    }
}
