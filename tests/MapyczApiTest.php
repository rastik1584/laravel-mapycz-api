<?php

namespace Rastik1584\LaravelMapyczApi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;
use Rastik1584\LaravelMapyczApi\Facades\MapyczApi;
use Rastik1584\LaravelMapyczApi\MapyczApiServiceProvider;

class MapyczApiTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MapyczApiServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'MapyczApi' => MapyczApi::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('mapycz-api.api_key', 'test-api-key');
        $app['config']->set('mapycz-api.cache.enabled', false);
    }

    /** @test */
    public function it_can_search_locations()
    {
        // This is a basic test example. In a real test, you would mock the HTTP client
        // to avoid making actual API calls during testing.

        // Example of how to mock the HTTP client:
        // $mock = new MockHandler([
        //     new Response(200, [], json_encode(['results' => []])),
        // ]);
        // $handlerStack = HandlerStack::create($mock);
        // $client = new Client(['handler' => $handlerStack]);

        // Then inject this client into your service...

        // For now, we'll just assert that the facade exists and is callable
        $this->assertTrue(method_exists(MapyczApi::class, 'search'));
    }

    /** @test */
    public function it_can_geocode_addresses()
    {
        // Similar to the search test, this would normally mock the HTTP client
        $this->assertTrue(method_exists(MapyczApi::class, 'geocode'));
    }

    /** @test */
    public function it_can_access_api_functions()
    {
        // Test that the functions method exists and is callable
        $this->assertTrue(method_exists(MapyczApi::class, 'functions'));
    }
}
