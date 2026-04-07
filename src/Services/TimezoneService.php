<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Services;

use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneCoordinateParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneListParams;
use Rastik1584\LaravelMapyczApi\DTO\Timezone\TimezoneNameParams;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;
use Rastik1584\LaravelMapyczApi\Responses\TimezoneInfoResult;
use Rastik1584\LaravelMapyczApi\Responses\TimezoneListResult;

class TimezoneService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws MapyczApiRequestException
     */
    public function list(array|TimezoneListParams $params = []): array
    {
        return $this->client->get('timezone/list-timezones', $this->normalizeList($params))->json();
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function listResult(array|TimezoneListParams $params = []): TimezoneListResult
    {
        return TimezoneListResult::fromArray($this->list($params));
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezone(string|array|TimezoneNameParams $params, array $options = []): array
    {
        return $this->client->get('timezone/timezone', $this->normalizeTimezone($params, $options))->json();
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function timezoneResult(string|array|TimezoneNameParams $params, array $options = []): TimezoneInfoResult
    {
        return TimezoneInfoResult::fromArray($this->timezone($params, $options));
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function coordinate(float|array|TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = []): array
    {
        return $this->client->get('timezone/coordinate', $this->normalizeCoordinate($lonOrParams, $lat, $options))->json();
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function coordinateResult(float|array|TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = []): TimezoneInfoResult
    {
        return TimezoneInfoResult::fromArray($this->coordinate($lonOrParams, $lat, $options));
    }

    protected function normalizeList(array|TimezoneListParams $params): array
    {
        return $params instanceof TimezoneListParams ? $params->toArray() : $params;
    }

    protected function normalizeTimezone(string|array|TimezoneNameParams $params, array $options = []): array
    {
        if ($params instanceof TimezoneNameParams) {
            return $params->toArray() + $options;
        }

        if (is_string($params)) {
            return ['timezone' => $params] + $options;
        }

        if (! isset($params['timezone']) || trim((string) $params['timezone']) === '') {
            throw new InvalidMapyczParamsException('Parameter "timezone" is required.');
        }

        return $params + $options;
    }

    protected function normalizeCoordinate(float|array|TimezoneCoordinateParams $lonOrParams, ?float $lat = null, array $options = []): array
    {
        if ($lonOrParams instanceof TimezoneCoordinateParams) {
            return $lonOrParams->toArray() + $options;
        }

        if (is_array($lonOrParams)) {
            if (! isset($lonOrParams['lon'], $lonOrParams['lat'])) {
                throw new InvalidMapyczParamsException('Parameters "lon" and "lat" are required.');
            }

            return $lonOrParams + $options;
        }

        if ($lat === null) {
            throw new InvalidMapyczParamsException('Latitude must be provided when calling coordinate() with longitude as float.');
        }

        return ['lon' => $lonOrParams, 'lat' => $lat] + $options;
    }
}
