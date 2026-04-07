<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class ElevationResult
{
    /**
     * @param  array<int, ElevationItem>  $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            items: array_map(
                static fn (array $item): ElevationItem => ElevationItem::fromArray($item),
                $payload['items'] ?? []
            ),
        );
    }
}

final class ElevationItem
{
    public function __construct(
        public readonly float $elevation,
        public readonly ElevationPosition $position,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            elevation: (float) ($payload['elevation'] ?? 0.0),
            position: ElevationPosition::fromArray($payload['position'] ?? []),
        );
    }
}

final class ElevationPosition
{
    public function __construct(
        public readonly float $lon,
        public readonly float $lat,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            lon: (float) ($payload['lon'] ?? 0.0),
            lat: (float) ($payload['lat'] ?? 0.0),
        );
    }
}
