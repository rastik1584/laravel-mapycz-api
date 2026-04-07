<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class TimezoneListResult
{
    /**
     * @param  array<int, string>  $timezones
     */
    public function __construct(
        public readonly array $timezones,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self($payload['timezones'] ?? []);
    }
}
