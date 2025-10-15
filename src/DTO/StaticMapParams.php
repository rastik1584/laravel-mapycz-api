<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use Exception;
use InvalidArgumentException;

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
            throw new InvalidArgumentException("Width and height must be greater than 0.");
        }

        if ($this->width > $this->maxWidth || $this->height > $this->maxHeight) {
            throw new InvalidArgumentException("Width and height must be less than 1024.");
        }

        if ($this->scale !== 1 && $this->scale !== 2) {
            throw new InvalidArgumentException("Scale must be 1 or 2.");
        }

        if ($this->zoom !== null && ($this->zoom < 1 || $this->zoom > 20)) {
            throw new InvalidArgumentException("Zoom must be between 1 and 20.");
        }

        if ($this->padding !== null && ($this->padding < $this->minPadding || $this->padding > $this->maxPadding)) {
            throw new InvalidArgumentException("Padding must be between 0 and 1024.");
        }

        if (blank($this->latitude) || blank($this->longitude) && !blank($this->markers) && is_array($this->markers) && count($this->markers) > 0) {
            throw new InvalidArgumentException("Latitude and longitude must be set or markers must be set.");
        }

        foreach ($this->markers as $marker) {
            if (!preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?(,[a-z]+)?$/', $marker)) {
                throw new InvalidArgumentException("Invalid marker format: $marker");
            }
        }
    }

    public function toArray(): array
    {
        $params = [
            'width' => $this->width,
            'height' => $this->height,
            'lon' => $this->longitude,
            'lat' => $this->latitude,
            'zoom' => is_null($this->zoom) ? 7 : $this->zoom,
            'scale' => $this->scale,
            'padding' => is_null($this->padding) ? 40 : $this->padding,
            'format' => $this->imageFormat,
            'mapSet' => $this->mapSet,
            'lang' => $this->lang,
        ];

        if (!empty($this->markers)) {
            $params['markers'] = $this->buildMarkers();
        }
        if (!empty($this->shapes)) {
            $params['shapes'] = implode('|', $this->shapes);
        }
        if ($this->debug) $params['debug'] = true;

        return $params;
    }

    protected function buildMarkers(): array
    {
        $result = [];

        foreach ($this->markers as $markerGroup) {
            $parts = [];

            if (!empty($markerGroup['color'])) {
                $parts[] = 'color:' . $markerGroup['color'];
            }

            if (!empty($markerGroup['size'])) {
                $parts[] = 'size:' . $markerGroup['size'];
            }

            if (!empty($markerGroup['label_color'])) {
                $parts[] = 'label-color:' . $markerGroup['label_color'];
            }

            foreach ($markerGroup['points'] as $point) {
                if (isset($point['label'])) {
                    $label = $point['label'];

                    if (!is_string($label) || mb_strlen($label) > 2) {
                        throw new \InvalidArgumentException("Marker label '{$label}' is invalid. It must be a string with max 2 characters.");
                    }

                    $parts[] = 'label:' . $label;
                }

                if (!isset($point['lon'], $point['lat'])) {
                    throw new \InvalidArgumentException("Each marker point must contain both 'lon' and 'lat'.");
                }

                $parts[] = $point['lon'] . ',' . $point['lat'];
            }

            $result[] = implode(';', $parts);
        }

        return $result;
    }


}
