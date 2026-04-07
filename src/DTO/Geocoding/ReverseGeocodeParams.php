<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Geocoding;

use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

final class ReverseGeocodeParams extends BaseDTO
{
    public function __construct(
        public float $lat,
        public float $lon,
        public ?string $lang = 'cs',
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (! is_finite($this->lat) || $this->lat < -90 || $this->lat > 90) {
            throw new InvalidMapyczParamsException('Latitude must be between -90 and 90.');
        }
        if (! is_finite($this->lon) || $this->lon < -180 || $this->lon > 180) {
            throw new InvalidMapyczParamsException('Longitude must be between -180 and 180.');
        }
        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
    }

    public function toArray(): array
    {
        $params = [
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];
        if (! is_null($this->lang)) {
            $params['lang'] = $this->lang;
        }

        return $params;
    }
}
