<?php

namespace Rastik1584\LaravelMapyczApi\Services;

use Illuminate\Support\Facades\Storage;
use Rastik1584\LaravelMapyczApi\MapyczApiClient;

class StaticMapService
{
    public function __construct(protected MapyczApiClient $client) {}


    public function image(string $path, array $params, string $filename = '', string $disk = 'public'): string
    {
        try {
            $response = $this->client->get(endpoint: '/static/map', params: $params);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $filename = blank($filename) ? date_format(now(), 'YmdHis') . '.png' : $filename;

        Storage::disk($disk)->put($path . '/' . $filename, $response->getBody());

        return $filename;
    }
}
