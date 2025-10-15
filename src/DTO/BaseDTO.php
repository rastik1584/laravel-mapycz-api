<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use InvalidArgumentException;

abstract class BaseDTO
{
    /**
     * @throws \Exception
     */
    protected function validateLang(string $lang): bool
    {
        if (!in_array($lang, config('mapycz-api.allowed_params.lang', ['cs']))) {
            throw new \Exception("Invalid language : $lang");
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    protected function validateImageFormat(string $format): bool
    {
        if (!in_array($format, config('mapycz-api.allowed_params.imageFormats', ['png', 'jpg']))) {
            throw new InvalidArgumentException("Invalid image format : $format");
        }
        return true;
    }

    protected function validateMapSets(string $mapSet): bool
    {
        if (!in_array($mapSet, config('mapycz-api.allowed_params.mapSets', ['basic', 'outdoor', 'aerial', 'winter', 'aerial-names-overlay']))) {
            throw new InvalidArgumentException("Unsupported mapSet: {$mapSet}");
        }
        return true;
    }

    protected function validateCoordinatesArray(array $coords): bool
    {

        foreach ($coords as $point) {
            if (!is_array($point)) {
                return false;
            }

            if (count($point) !== 2) {
                return false;
            }

            [$lat, $lng] = $point;

            if (!is_numeric($lat) || !is_numeric($lng)) {
                return false;
            }

            if ($lat < -90 || $lat > 90) {
                return false;
            }
            if ($lng < -180 || $lng > 180) {
                return false;
            }
        }
        return true;
    }
}