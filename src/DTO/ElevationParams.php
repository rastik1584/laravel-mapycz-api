<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO;

use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

final class ElevationParams extends BaseDTO
{
    /**
     * @param  array<int, array<int|string, float|int|string>>  $positions
     */
    public function __construct(
        public array $positions,
        public ?string $lang = 'cs',
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }

        if (count($this->positions) < 1 || count($this->positions) > 256) {
            throw new InvalidMapyczParamsException('Positions must contain between 1 and 256 coordinates.');
        }

        foreach ($this->positions as $position) {
            $this->normalizeCoordinates($position);
        }
    }

    public function toArray(): array
    {
        $params = [
            'positions' => array_map(
                fn (array $position): string => implode(',', $this->normalizeCoordinates($position)),
                $this->positions
            ),
        ];

        if ($this->lang !== null && $this->lang !== 'cs') {
            $params['lang'] = $this->lang;
        }

        return $params;
    }
}
