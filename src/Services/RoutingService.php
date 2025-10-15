<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class RoutingService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws RequestException
     */
    public function route(RouteMapParams|array $params): JsonResponse|array
    {
        try {
            return $this->client->get(endpoint: '/routing/route', params: $params)->json();
        } catch (RequestException $e) {
            Log::error("Error Laravel-mapycz-api Routing Service:". $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws RequestException
     */
    public function matrixM(MatrixMapParams|array $params): JsonResponse|array
    {
        try {
            return $this->client->get(endpoint: '/routing/matrix-m', params: $params)->json();
        } catch (RequestException $e) {
            Log::error("Error Laravel-mapycz-api Routing Service:". $e->getMessage());
            throw $e;
        }
    }
}
