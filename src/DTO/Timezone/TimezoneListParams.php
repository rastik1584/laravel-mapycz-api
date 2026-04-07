<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Timezone;

use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;

final class TimezoneListParams extends BaseDTO
{
    public function __construct(
        public ?string $lang = 'cs',
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
    }

    public function toArray(): array
    {
        if ($this->lang === null || $this->lang === 'cs') {
            return [];
        }

        return ['lang' => $this->lang];
    }
}
