<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Log;
use Rastik1584\LaravelMapyczApi\DTO\Routing\MatrixMapParams;
use Rastik1584\LaravelMapyczApi\DTO\Routing\RouteMapParams;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;
use Rastik1584\LaravelMapyczApi\Responses\MatrixResult;
use Rastik1584\LaravelMapyczApi\Responses\RoutingResult;

class RoutingService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws MapyczApiRequestException
     */
    public function route(RouteMapParams|array $params): array
    {
        try {
            return $this->client
                ->get(endpoint: 'routing/route', params: $this->normalizeRouteParams($params))
                ->json();
        } catch (MapyczApiRequestException $e) {
            Log::error('Error Laravel-mapycz-api Routing Service: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function routeResult(RouteMapParams|array $params): RoutingResult
    {
        return RoutingResult::fromArray($this->route($params));
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function matrixM(MatrixMapParams|array $params): array
    {
        try {
            return $this->client
                ->get(endpoint: 'routing/matrix-m', params: $this->normalizeMatrixParams($params))
                ->json();
        } catch (MapyczApiRequestException $e) {
            Log::error('Error Laravel-mapycz-api Routing Service: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function matrixResult(MatrixMapParams|array $params): MatrixResult
    {
        return MatrixResult::fromArray($this->matrixM($params));
    }

    protected function normalizeRouteParams(RouteMapParams|array $params): array
    {
        return $params instanceof RouteMapParams ? $params->toArray() : $params;
    }

    protected function normalizeMatrixParams(MatrixMapParams|array $params): array
    {
        return $params instanceof MatrixMapParams ? $params->toArray() : $params;
    }
}
