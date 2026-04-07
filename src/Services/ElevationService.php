<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Services;

use Rastik1584\LaravelMapyczApi\DTO\ElevationParams;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;
use Rastik1584\LaravelMapyczApi\Responses\ElevationResult;

class ElevationService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws MapyczApiRequestException
     */
    public function elevations(array|ElevationParams $params, array $options = []): array
    {
        $payload = $this->normalize($params, $options);

        return $this->client->get('elevation', $payload)->json();
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function elevationsResult(array|ElevationParams $params, array $options = []): ElevationResult
    {
        return ElevationResult::fromArray($this->elevations($params, $options));
    }

    protected function normalize(array|ElevationParams $params, array $options = []): array
    {
        if ($params instanceof ElevationParams) {
            return $params->toArray() + $options;
        }

        if (! isset($params['positions']) || ! is_array($params['positions'])) {
            throw new InvalidMapyczParamsException('Parameter "positions" is required and must be an array.');
        }

        return $params + $options;
    }
}
