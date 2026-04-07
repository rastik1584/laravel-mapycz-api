<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use Exception;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

/**
 * https://api.mapy.com/v1/docs/static/#/static/api_staticpano_v1_static_pano_get
 */
final class StaticPanoramaParams extends BaseDTO
{
    /**
     * @throws Exception
     */
    public function __construct(
        public int $width = 400,
        public int $height = 225,
        public float $lon = 0.0,
        public float $lat = 0.0,
        public float $radius = 50.0,
        public string|float $yaw = 'auto',
        public float $pitch = -0.066,
        public float $fov = 1.5,
        public string $lang = 'cs',
        public ?bool $debug = null,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        $this->validateLang($this->lang);
        $this->normalizeCoordinates([$this->lon, $this->lat]);

        if ($this->width < 10 || $this->width > 1024) {
            throw new InvalidMapyczParamsException('Width must be between 10 and 1024.');
        }

        if ($this->height < 10 || $this->height > 1024) {
            throw new InvalidMapyczParamsException('Height must be between 10 and 1024.');
        }

        if ($this->radius < 0 || $this->radius > 100) {
            throw new InvalidMapyczParamsException('Radius must be between 0 and 100.');
        }

        if ($this->pitch < -1.5 || $this->pitch > 1.5) {
            throw new InvalidMapyczParamsException('Pitch must be between -1.5 and 1.5.');
        }

        if ($this->fov < 0.16 || $this->fov > 1.57) {
            throw new InvalidMapyczParamsException('Fov must be between 0.16 and 1.57.');
        }

        if (! is_float($this->yaw) && ! in_array($this->yaw, ['auto', 'point'], true)) {
            throw new InvalidMapyczParamsException('Yaw must be "auto", "point", or a float value.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'width' => $this->width,
            'height' => $this->height,
            'lon' => $this->lon,
            'lat' => $this->lat,
            'radius' => $this->radius,
            'yaw' => $this->yaw,
            'pitch' => $this->pitch,
            'fov' => $this->fov,
            'lang' => $this->lang,
        ];

        if ($this->debug) {
            $params['debug'] = true;
        }

        if ($this->lang === 'cs') {
            unset($params['lang']);
        }

        return $params;
    }
}
