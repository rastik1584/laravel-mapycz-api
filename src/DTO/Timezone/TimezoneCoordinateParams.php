<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Timezone;

use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;

final class TimezoneCoordinateParams extends BaseDTO
{
    public function __construct(
        public float $lon,
        public float $lat,
        public ?string $lang = 'cs',
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        $this->normalizeCoordinates([$this->lon, $this->lat]);

        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
    }

    public function toArray(): array
    {
        $params = [
            'lon' => $this->lon,
            'lat' => $this->lat,
        ];

        if ($this->lang !== null && $this->lang !== 'cs') {
            $params['lang'] = $this->lang;
        }

        return $params;
    }
}
