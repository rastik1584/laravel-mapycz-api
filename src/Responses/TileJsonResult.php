<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class TileJsonResult
{
    /**
     * @param  array<int, string>  $tiles
     * @param  array<int, string>  $grids
     * @param  array<int, string>  $data
     * @param  array<int, float|int>  $bounds
     * @param  array<int, float|int>  $center
     */
    public function __construct(
        public readonly string $tilejson,
        public readonly array $tiles,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $version = null,
        public readonly ?string $attribution = null,
        public readonly ?string $template = null,
        public readonly ?string $legend = null,
        public readonly ?string $scheme = null,
        public readonly array $grids = [],
        public readonly array $data = [],
        public readonly ?int $minzoom = null,
        public readonly ?int $maxzoom = null,
        public readonly array $bounds = [],
        public readonly array $center = [],
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            tilejson: (string) ($payload['tilejson'] ?? ''),
            tiles: $payload['tiles'] ?? [],
            name: isset($payload['name']) ? (string) $payload['name'] : null,
            description: isset($payload['description']) ? (string) $payload['description'] : null,
            version: isset($payload['version']) ? (string) $payload['version'] : null,
            attribution: isset($payload['attribution']) ? (string) $payload['attribution'] : null,
            template: isset($payload['template']) ? (string) $payload['template'] : null,
            legend: isset($payload['legend']) ? (string) $payload['legend'] : null,
            scheme: isset($payload['scheme']) ? (string) $payload['scheme'] : null,
            grids: $payload['grids'] ?? [],
            data: $payload['data'] ?? [],
            minzoom: isset($payload['minzoom']) ? (int) $payload['minzoom'] : null,
            maxzoom: isset($payload['maxzoom']) ? (int) $payload['maxzoom'] : null,
            bounds: $payload['bounds'] ?? [],
            center: $payload['center'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'tilejson' => $this->tilejson,
            'tiles' => $this->tiles,
            'name' => $this->name,
            'description' => $this->description,
            'version' => $this->version,
            'attribution' => $this->attribution,
            'template' => $this->template,
            'legend' => $this->legend,
            'scheme' => $this->scheme,
            'grids' => $this->grids,
            'data' => $this->data,
            'minzoom' => $this->minzoom,
            'maxzoom' => $this->maxzoom,
            'bounds' => $this->bounds,
            'center' => $this->center,
        ];
    }
}
