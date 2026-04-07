<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Storage;
use Rastik1584\LaravelMapyczApi\DTO\StaticMapParams;
use Rastik1584\LaravelMapyczApi\DTO\StaticPanoramaParams;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class StaticMapService
{
    public function __construct(protected MapyczApiClient $client) {}

    /**
     * @throws MapyczApiRequestException
     */
    public function image(string $storagePath, StaticMapParams|array $params, string $filename = '', string $disk = 'public'): string
    {
        $response = $this->client->get(
            endpoint: 'static/map',
            params: $params instanceof StaticMapParams ? $params->toArray() : $params
        );

        $filename = blank($filename) ? date_format(now(), 'YmdHis').'.png' : $filename;

        Storage::disk($disk)->put($storagePath.'/'.$filename, $response->body());

        return $filename;
    }

    /**
     * @throws MapyczApiRequestException
     */
    public function panorama(string $storagePath, StaticPanoramaParams|array $params, string $filename = '', string $disk = 'public'): string
    {
        $response = $this->client->get(
            endpoint: 'static/pano',
            params: $params instanceof StaticPanoramaParams ? $params->toArray() : $params
        );

        $filename = blank($filename) ? date_format(now(), 'YmdHis').'.jpg' : $filename;

        Storage::disk($disk)->put($storagePath.'/'.$filename, $response->body());

        return $filename;
    }
}
