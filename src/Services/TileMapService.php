<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Storage;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;
use Rastik1584\LaravelMapyczApi\Responses\TileJsonResult;

class TileMapService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws MapyczApiRequestException
     */
    public function tile(string $mapset, int $zoom, int $x, int $y, string $path, string $tileSize = '256', string $filename = '', string $disk = 'public', ?string $lang = null): string
    {
        if (! in_array($mapset, config('mapycz-api.allowed_params.mapSets', []), true)) {
            throw new InvalidMapyczParamsException('Unsupported mapset: '.$mapset);
        }
        if (! in_array($tileSize, config('mapycz-api.allowed_params.tileSizes', ['256', '256@2x']), true)) {
            throw new InvalidMapyczParamsException('Unsupported tile size: '.$tileSize);
        }
        if ($zoom < 0 || $zoom > 20) {
            throw new InvalidMapyczParamsException('Zoom must be between 0 and 20.');
        }
        if ($x < 0 || $y < 0) {
            throw new InvalidMapyczParamsException('Tile coordinates must be zero or greater.');
        }

        $response = $this->client->get(
            endpoint: "maptiles/$mapset/$tileSize/$zoom/$x/$y",
            params: array_filter(['lang' => $lang], static fn ($value) => $value !== null)
        );
        $filename = blank($filename) ? date_format(now(), 'YmdHis').'.png' : $filename;

        Storage::disk($disk)->put($path.'/'.$filename, $response->body());

        return $filename;
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function tileJson(string $mapset, ?string $lang = null): array
    {
        if (! in_array($mapset, config('mapycz-api.allowed_params.mapSets', []), true)) {
            throw new InvalidMapyczParamsException('Unsupported mapset: '.$mapset);
        }

        return $this->client->get(
            endpoint: "maptiles/$mapset/tiles.json",
            params: array_filter(['lang' => $lang], static fn ($value) => $value !== null)
        )->json();
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function tileJsonResult(string $mapset, ?string $lang = null): TileJsonResult
    {
        return TileJsonResult::fromArray($this->tileJson($mapset, $lang));
    }
}
