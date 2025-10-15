<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi;

use Illuminate\Http\Client\RequestException;
use Rastik1584\LaravelMapyczApi\Services\GeocodingService;
use Rastik1584\LaravelMapyczApi\Services\RoutingService;

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

    // Convenience shortcuts
    public function geocode(string $query, array $options = []): array
    {
        return $this->geocoding()->geocode($query, $options);
    }

    public function suggest(string $query, array $options = []): array
    {
        return $this->geocoding()->suggest($query, $options);
    }

    public function reverse(float $lat, float $lon, array $options = []): array
    {
        return $this->geocoding()->reverse($lat, $lon, $options);
    }

    /**
     * Backward-compatible generic search shortcut -> maps to suggest endpoint.
     * @throws RequestException
     */
    public function search(string $query, array $options = []): array
    {
        return $this->suggest($query, $options);
    }

    /**
     * Generic passthrough to the functions (funkce) endpoint.
     * @throws RequestException
     */
    public function functions(string $function, array $params = []): array
    {
        return $this->client->get('functions/' . ltrim($function, '/'), $params)->json();
    }
}
