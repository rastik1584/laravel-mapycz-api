<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use Exception;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

/**
 * https://api.mapy.com/v1/docs/static/#/static/api_staticmap_v1_static_map_get
 */
final class StaticMapParams extends BaseDTO
{
    protected int $maxWidth = 1024;

    protected int $minWidth = 10;

    protected int $maxHeight = 1024;

    protected int $minHeight = 10;

    protected int $minPadding = 0;

    protected int $maxPadding = 1024;

    protected int $maxZoom = 19;

    /**
     * @throws Exception
     */
    public function __construct(
        public string $mapSet = 'basic',
        public int $width = 800,
        public int $height = 600,
        public int $scale = 1,
        public string $imageFormat = 'png',
        public string $lang = 'cs',
        public string|array|null $latitude = '',
        public string|array|null $longitude = '',
        public ?int $zoom = null,
        public ?int $padding = null,
        public array $markers = [],
        public array $shapes = [],
        public ?bool $debug = null,
    ) {
        $this->validate();
    }

    /**
     * @throws Exception
     */
    protected function validate(): void
    {
        $this->validateLang($this->lang);
        $this->validateImageFormat($this->imageFormat);
        $this->validateMapSets($this->mapSet);

        if ($this->width < $this->minWidth || $this->height < $this->minHeight) {
            throw new InvalidMapyczParamsException('Width and height must be greater than 0.');
        }

        if ($this->width > $this->maxWidth || $this->height > $this->maxHeight) {
            throw new InvalidMapyczParamsException('Width and height must be less than 1024.');
        }

        if ($this->scale !== 1 && $this->scale !== 2) {
            throw new InvalidMapyczParamsException('Scale must be 1 or 2.');
        }

        if ($this->zoom !== null && ($this->zoom < 1 || $this->zoom > $this->maxZoom)) {
            throw new InvalidMapyczParamsException("Zoom must be between 1 and {$this->maxZoom}.");
        }

        if ($this->padding !== null && ($this->padding < $this->minPadding || $this->padding > $this->maxPadding)) {
            throw new InvalidMapyczParamsException('Padding must be between 0 and 1024.');
        }

        $hasLongitude = ! blank($this->longitude);
        $hasLatitude = ! blank($this->latitude);
        $hasMarkers = ! empty($this->markers);

        if ($hasLongitude xor $hasLatitude) {
            throw new InvalidMapyczParamsException('Latitude and longitude must be provided together.');
        }

        if (! $hasMarkers && ! $hasLongitude && ! $hasLatitude) {
            throw new InvalidMapyczParamsException('Either map coordinates or markers must be provided.');
        }

        if ($hasLongitude && $hasLatitude) {
            $lonValues = is_array($this->longitude) ? $this->longitude : [$this->longitude];
            $latValues = is_array($this->latitude) ? $this->latitude : [$this->latitude];

            if (count($lonValues) !== count($latValues) || count($lonValues) < 1 || count($lonValues) > 2) {
                throw new InvalidMapyczParamsException('Longitude and latitude must contain one or two matching values.');
            }

            foreach ($lonValues as $index => $lon) {
                $this->normalizeCoordinates([
                    (float) $lon,
                    (float) $latValues[$index],
                ]);
            }

            if (count($lonValues) === 1 && $this->zoom === null) {
                throw new InvalidMapyczParamsException('Zoom is required when a single viewport center is provided.');
            }
        }

        foreach ($this->markers as $markerGroup) {
            $this->validateMarkerGroup($markerGroup);
        }

        foreach ($this->shapes as $shape) {
            $this->validateShape($shape);
        }
    }

    public function toArray(): array
    {
        $params = [
            'width' => $this->width,
            'height' => $this->height,
            'lon' => $this->longitude,
            'lat' => $this->latitude,
            'zoom' => $this->zoom,
            'scale' => $this->scale,
            'padding' => is_null($this->padding) ? 40 : $this->padding,
            'format' => $this->imageFormat,
            'mapset' => $this->mapSet,
            'lang' => $this->lang,
        ];

        if (! empty($this->markers)) {
            $params['markers'] = $this->buildMarkers();
        }
        if (! empty($this->shapes)) {
            $params['shapes'] = $this->buildShapes();
        }
        if ($this->debug) {
            $params['debug'] = true;
        }

        return array_filter(
            $params,
            static fn ($value): bool => $value !== null && $value !== []
        );
    }

    protected function buildMarkers(): array
    {
        $result = [];

        foreach ($this->markers as $markerGroup) {
            if (is_string($markerGroup)) {
                $result[] = $markerGroup;

                continue;
            }

            $parts = [];

            if (! empty($markerGroup['color'])) {
                $parts[] = 'color:'.$markerGroup['color'];
            }

            if (! empty($markerGroup['size'])) {
                $parts[] = 'size:'.$markerGroup['size'];
            }

            if (! empty($markerGroup['label_color'])) {
                $parts[] = 'label-color:'.$markerGroup['label_color'];
            }

            foreach ($markerGroup['points'] as $point) {
                [$lon, $lat] = $this->normalizeCoordinates($point);

                if (isset($point['label'])) {
                    $label = $point['label'];

                    if (! is_string($label) || mb_strlen($label) > 2) {
                        throw new InvalidMapyczParamsException("Marker label '{$label}' is invalid. It must be a string with max 2 characters.");
                    }

                    $parts[] = 'label:'.$label;
                }

                $parts[] = $lon.','.$lat;
            }

            $result[] = implode(';', $parts);
        }

        return $result;
    }

    protected function validateMarkerGroup(mixed $markerGroup): void
    {
        if (is_string($markerGroup)) {
            if (trim($markerGroup) === '') {
                throw new InvalidMapyczParamsException('Marker string must not be empty.');
            }

            return;
        }

        if (! is_array($markerGroup) || empty($markerGroup['points']) || ! is_array($markerGroup['points'])) {
            throw new InvalidMapyczParamsException('Marker groups must be strings or arrays with a points key.');
        }

        foreach ($markerGroup['points'] as $point) {
            $this->normalizeCoordinates($point);

            if (isset($point['label']) && (! is_string($point['label']) || mb_strlen($point['label']) > 2)) {
                throw new InvalidMapyczParamsException('Marker label must be a string with max 2 characters.');
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function buildShapes(): array
    {
        $result = [];

        foreach ($this->shapes as $shape) {
            if (is_string($shape)) {
                $result[] = $shape;

                continue;
            }

            $parts = [];

            if (! empty($shape['color'])) {
                $parts[] = 'color:'.$shape['color'];
            }

            if (! empty($shape['fill'])) {
                $parts[] = 'fill:'.$shape['fill'];
            }

            if (isset($shape['width'])) {
                $parts[] = 'width:'.(int) $shape['width'];
            }

            if (isset($shape['path'])) {
                $parts[] = 'path:'.$this->serializeShapePath($shape['path']);
            }

            if (isset($shape['polygon'])) {
                $parts[] = 'polygon:'.$this->serializeShapePolygon($shape['polygon']);
            }

            $result[] = implode(';', $parts);
        }

        return $result;
    }

    protected function validateShape(mixed $shape): void
    {
        if (is_string($shape)) {
            if (trim($shape) === '') {
                throw new InvalidMapyczParamsException('Each shape string must be non-empty.');
            }

            return;
        }

        if (! is_array($shape)) {
            throw new InvalidMapyczParamsException('Each shape must be a string or an array definition.');
        }

        $hasPath = array_key_exists('path', $shape);
        $hasPolygon = array_key_exists('polygon', $shape);

        if ($hasPath === $hasPolygon) {
            throw new InvalidMapyczParamsException('Each shape must define exactly one of "path" or "polygon".');
        }

        if (isset($shape['width']) && (! is_numeric($shape['width']) || (int) $shape['width'] < 1)) {
            throw new InvalidMapyczParamsException('Shape width must be a positive integer.');
        }

        if ($hasPath) {
            $this->validateShapePath($shape['path']);
        }

        if ($hasPolygon) {
            $this->validateShapePolygon($shape['polygon']);
        }
    }

    protected function validateShapePath(mixed $path): void
    {
        if (is_string($path)) {
            if (trim($path) === '') {
                throw new InvalidMapyczParamsException('Shape path string must not be empty.');
            }

            return;
        }

        if (! is_array($path) || count($path) < 2) {
            throw new InvalidMapyczParamsException('Shape path must contain at least two coordinates.');
        }

        foreach ($path as $point) {
            $this->normalizeCoordinates($point);
        }
    }

    protected function validateShapePolygon(mixed $polygon): void
    {
        if (is_string($polygon)) {
            if (trim($polygon) === '') {
                throw new InvalidMapyczParamsException('Shape polygon string must not be empty.');
            }

            return;
        }

        if (! is_array($polygon) || count($polygon) < 1) {
            throw new InvalidMapyczParamsException('Shape polygon must contain at least one ring.');
        }

        foreach ($polygon as $ring) {
            if (! is_array($ring) || count($ring) < 3) {
                throw new InvalidMapyczParamsException('Each polygon ring must contain at least three coordinates.');
            }

            foreach ($ring as $point) {
                $this->normalizeCoordinates($point);
            }
        }
    }

    protected function serializeShapePath(mixed $path): string
    {
        if (is_string($path)) {
            return $path;
        }

        $points = array_map(
            fn (array $point): string => implode(',', $this->normalizeCoordinates($point)),
            $path
        );

        return '[('.implode(';', $points).')]';
    }

    protected function serializeShapePolygon(mixed $polygon): string
    {
        if (is_string($polygon)) {
            return $polygon;
        }

        $rings = array_map(function (array $ring): string {
            $points = array_map(
                fn (array $point): string => implode(',', $this->normalizeCoordinates($point)),
                $ring
            );

            return '('.implode(';', $points).')';
        }, $polygon);

        return '['.implode(',', $rings).']';
    }
}
