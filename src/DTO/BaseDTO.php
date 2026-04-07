<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

abstract class BaseDTO
{
    protected function validateLang(string $lang): bool
    {
        if (! in_array($lang, config('mapycz-api.allowed_params.lang', ['cs']))) {
            throw new InvalidMapyczParamsException("Invalid language: {$lang}");
        }

        return true;
    }

    protected function validateImageFormat(string $format): bool
    {
        if (! in_array($format, config('mapycz-api.allowed_params.imageFormats', ['png', 'jpg']))) {
            throw new InvalidMapyczParamsException("Invalid image format: {$format}");
        }

        return true;
    }

    protected function validateMapSets(string $mapSet): bool
    {
        if (! in_array($mapSet, config('mapycz-api.allowed_params.mapSets', ['basic', 'outdoor', 'aerial', 'winter', 'aerial-names-overlay']))) {
            throw new InvalidMapyczParamsException("Unsupported mapSet: {$mapSet}");
        }

        return true;
    }

    protected function validateCoordinatesArray(array $coords): bool
    {
        foreach ($coords as $point) {
            if (! is_array($point)) {
                return false;
            }

            if (count($point) !== 2) {
                return false;
            }

            [$lon, $lat] = array_values($point);

            if (! is_numeric($lon) || ! is_numeric($lat)) {
                return false;
            }

            if ($lon < -180 || $lon > 180) {
                return false;
            }
            if ($lat < -90 || $lat > 90) {
                return false;
            }
        }

        return true;
    }

    protected function supportedRouteTypes(): array
    {
        return config('mapycz-api.allowed_params.routeTypes', [
            'car_fast',
            'car_fast_traffic',
            'car_short',
            'foot_fast',
            'foot_hiking',
            'bike_road',
            'bike_mountain',
        ]);
    }

    /**
     * Normalize coordinate input and return [longitude, latitude].
     */
    protected function normalizeCoordinates(array $coords): array
    {
        if (isset($coords['lat']) && (isset($coords['lon']) || isset($coords['lng']))) {
            return [
                (float) ($coords['lon'] ?? $coords['lng']),
                (float) $coords['lat'],
            ];
        }

        if (isset($coords[0], $coords[1])) {
            $first = (float) $coords[0];
            $second = (float) $coords[1];

            if (abs($first) <= 180 && abs($second) <= 90) {
                return [$first, $second];
            }

            if (abs($first) <= 90 && abs($second) <= 180) {
                return [$second, $first];
            }
        }

        throw new InvalidMapyczParamsException('Invalid coordinate input.');
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    protected function normalizeCoordinatesBatch(array $points): array
    {
        return array_map(fn (array $point): array => $this->normalizeCoordinates($point), $points);
    }
}
