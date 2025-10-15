<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Rastik1584\LaravelMapyczApi\DTO\Geocoding\GeocodeParams;
use Rastik1584\LaravelMapyczApi\DTO\Geocoding\ReverseGeocodeParams;
use Rastik1584\LaravelMapyczApi\DTO\Geocoding\SuggestParams;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class GeocodingService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * Geocode a text query to coordinates and address details.
     * Accepts string query, array params, or GeocodeParams DTO.
     *
     * @throws RequestException
     */
    public function geocode(string|array|GeocodeParams $params, array $options = []): array
    {
        $payload = $this->normalizeGeocodeParams($params, $options);
        return $this->client->get('geocode', $payload)->json();
    }

    /**
     * Suggest places for autocomplete scenarios.
     * Accepts string query, array params, or SuggestParams DTO.
     *
     * @throws RequestException
     */
    public function suggest(string|array|SuggestParams $params, array $options = []): array
    {
        $payload = $this->normalizeGeocodeParams($params, $options);
        // Endpoint for suggest differs
        return $this->client->get('suggest', $payload)->json();
    }

    /**
     * Reverse geocode coordinates to address details.
     * Accepts lat/lon primitives, array params, or ReverseGeocodeParams DTO.
     *
     * @throws RequestException
     */
    public function reverse(float|array|ReverseGeocodeParams $latOrParams, ?float $lon = null, array $options = []): array
    {
        if ($latOrParams instanceof ReverseGeocodeParams) {
            $payload = $latOrParams->toArray() + $options;
        } elseif (is_array($latOrParams)) {
            $payload = $latOrParams + $options;
        } else {
            if ($lon === null) {
                throw new InvalidArgumentException('Longitude must be provided when calling reverse() with lat as float.');
            }
            $payload = ['lat' => $latOrParams, 'lon' => $lon] + $options;
        }
        return $this->client->get('reverse', $payload)->json();
    }

    /**
     * @param string|array|GeocodeParams|SuggestParams $params
     */
    protected function normalizeGeocodeParams(string|array|GeocodeParams|SuggestParams $params, array $options = []): array
    {
        if ($params instanceof GeocodeParams || $params instanceof SuggestParams) {
            return $params->toArray() + $options;
        }
        if (is_string($params)) {
            return ['query' => $params] + $options;
        }
        if (!isset($params['query']) || trim((string)$params['query']) === '') {
            throw new InvalidArgumentException('Parameter "query" is required.');
        }
        return $params + $options;
    }
}