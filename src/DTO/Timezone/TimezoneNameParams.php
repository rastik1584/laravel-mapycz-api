<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Timezone;

use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

final class TimezoneNameParams extends BaseDTO
{
    public function __construct(
        public string $timezone,
        public ?string $lang = 'cs',
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (trim($this->timezone) === '') {
            throw new InvalidMapyczParamsException('Timezone must not be empty.');
        }

        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
    }

    public function toArray(): array
    {
        $params = [
            'timezone' => $this->timezone,
        ];

        if ($this->lang !== null && $this->lang !== 'cs') {
            $params['lang'] = $this->lang;
        }

        return $params;
    }
}
