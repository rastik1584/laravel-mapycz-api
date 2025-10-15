<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Storage;
use Rastik1584\LaravelMapyczApi\DTO\StaticMapParams;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class StaticMapService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws \Exception
     */
    public function image(string $storagePath, StaticMapParams|array $params, string $filename = '', string $disk = 'public'): string
    {
        try {
            $response = $this->client->get(endpoint: '/static/map', params: $params);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $filename = blank($filename) ? date_format(now(), 'YmdHis') . '.png' : $filename;

        Storage::disk($disk)->put($storagePath . '/' . $filename, $response->getBody());

        return $filename;
    }

    public function panorama()
    {
        // TODO: https://api.mapy.com/v1/docs/static/#/static/api_staticpano_v1_static_pano_get
    }
}
