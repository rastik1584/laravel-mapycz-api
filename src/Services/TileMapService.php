<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Storage;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class TileMapService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @param string $mapset
     * @param int $zoom
     * @param int $x
     * @param int $y
     * @param int $tileSize
     * @return string
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function tile(string $mapset, int $zoom, int $x, int $y, string $path, int $tileSize = 256, string $filename = '', string $disk = 'public'): string
    {
        $response = $this->client->get(endpoint: "/maptiles/$mapset/$tileSize/$zoom/$x/$y");;
        $filename = blank($filename) ? date_format(now(), 'YmdHis') . '.png' : $filename;

        Storage::disk($disk)->put($path . '/' . $filename, $response->getBody());

        return $filename;
    }
}
