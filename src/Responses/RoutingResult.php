<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class RoutingResult
{
    /**
     * @param  array<int, RoutePart>  $parts
     * @param  array<int, RoutePoint>  $routePoints
     */
    public function __construct(
        public readonly int $length,
        public readonly int $duration,
        public readonly mixed $geometry,
        public readonly array $parts = [],
        public readonly array $routePoints = [],
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            length: (int) ($payload['length'] ?? 0),
            duration: (int) ($payload['duration'] ?? 0),
            geometry: $payload['geometry'] ?? null,
            parts: array_map(
                static fn (array $item): RoutePart => RoutePart::fromArray($item),
                $payload['parts'] ?? []
            ),
            routePoints: array_map(
                static fn (array $item): RoutePoint => RoutePoint::fromArray($item),
                $payload['routePoints'] ?? []
            ),
        );
    }
}

final class RoutePart
{
    public function __construct(
        public readonly int $length,
        public readonly int $duration,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            length: (int) ($payload['length'] ?? 0),
            duration: (int) ($payload['duration'] ?? 0),
        );
    }
}

final class RoutePoint
{
    /**
     * @param  array<int, float|int>  $originalPosition
     * @param  array<int, float|int>  $mappedPosition
     */
    public function __construct(
        public readonly array $originalPosition,
        public readonly array $mappedPosition,
        public readonly int $snapDistance,
        public readonly bool $restricted = false,
        public readonly ?string $restrictionType = null,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            originalPosition: $payload['originalPosition'] ?? [],
            mappedPosition: $payload['mappedPosition'] ?? [],
            snapDistance: (int) ($payload['snapDistance'] ?? 0),
            restricted: (bool) ($payload['restricted'] ?? false),
            restrictionType: isset($payload['restrictionType']) ? (string) $payload['restrictionType'] : null,
        );
    }
}
