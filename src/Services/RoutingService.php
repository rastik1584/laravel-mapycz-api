<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class RoutingService
{
    public function __construct(protected MapyczApiClient $client) {}

    public function route(string $start, string $end, string $waypoints, string $routeType, string $format = 'geojson', bool $avoidToll = false)
    {
        $params = [
            'start' => $start,
            'end' => $end,
            'routeType' => $routeType,
            'format' => $format,
            'avoidToll' => $avoidToll,
            'waypoints' => $waypoints,
        ];

        try {
            return $this->client->get(endpoint: '/routing/route', params: $params);
        } catch (HttpResponseException $e) {
            Log::error("Error Laravel-mapycz-api Routing Service:". $e->getMessage());
            throw $e;
        }
    }

    public function matrixM()
    {

    }
}
