<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class GeocodingService
{

    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws RequestException
     */
    public function geocode(string $query, array $options = []): JsonResponse
    {
        return $this->client->get('/geocode', array_merge(['query' => $query], $options));
    }

    /**
     * @throws RequestException
     */
    public function suggest(string $query, array $options = []): JsonResponse
    {
        return $this->client->get('/suggest', ['query' => $query] + $options);
    }

    /**
     * @throws RequestException
     */
    public function reverse(float $lat, float $lon, array $options = []): JsonResponse
    {
        return $this->client->get('/reverse', ['lat' => $lat, 'lon' => $lon] + $options);
    }

}