<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class GeocodeResult
{
    /**
     * @param  array<int, GeocodeItem>  $items
     * @param  array<int, array<string, string>>  $locality
     */
    public function __construct(
        public readonly array $items,
        public readonly array $locality = [],
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            items: array_map(
                static fn (array $item): GeocodeItem => GeocodeItem::fromArray($item),
                $payload['items'] ?? []
            ),
            locality: $payload['locality'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(static fn (GeocodeItem $item): array => $item->toArray(), $this->items),
            'locality' => $this->locality,
        ];
    }
}

final class GeocodeItem
{
    /**
     * @param  array<int, float|int>  $bbox
     * @param  array<int, RegionalEntity>  $regionalStructure
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly Coordinates $position,
        public readonly string $type,
        public readonly array $regionalStructure,
        public readonly ?string $location = null,
        public readonly ?string $zip = null,
        public readonly array $bbox = [],
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            name: (string) ($payload['name'] ?? ''),
            label: (string) ($payload['label'] ?? ''),
            position: Coordinates::fromArray($payload['position'] ?? []),
            type: (string) ($payload['type'] ?? ''),
            regionalStructure: array_map(
                static fn (array $item): RegionalEntity => RegionalEntity::fromArray($item),
                $payload['regionalStructure'] ?? []
            ),
            location: isset($payload['location']) ? (string) $payload['location'] : null,
            zip: isset($payload['zip']) ? (string) $payload['zip'] : null,
            bbox: $payload['bbox'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'position' => $this->position->toArray(),
            'type' => $this->type,
            'regionalStructure' => array_map(
                static fn (RegionalEntity $item): array => $item->toArray(),
                $this->regionalStructure
            ),
            'location' => $this->location,
            'zip' => $this->zip,
            'bbox' => $this->bbox,
        ];
    }
}

final class Coordinates
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

    public function toArray(): array
    {
        return [
            'lon' => $this->lon,
            'lat' => $this->lat,
        ];
    }
}

final class RegionalEntity
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $isoCode = null,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            name: (string) ($payload['name'] ?? ''),
            type: (string) ($payload['type'] ?? ''),
            isoCode: isset($payload['isoCode']) ? (string) $payload['isoCode'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'isoCode' => $this->isoCode,
        ];
    }
}
