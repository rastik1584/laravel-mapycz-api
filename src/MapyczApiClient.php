<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MapyczApiClient
{
    /**
     * @throws RequestException
     */
    public function get(string $endpoint, array $params = []): Response
    {
        if (blank(config('mapycz-api.api_key'))) {
            throw new \Exception('API key is not set.');
        }

        $request = Http::baseUrl(config('mapycz-api.base_url'));

        if (!config('mapycz-api.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request
            ->withQueryParameters(['apikey' => config('mapycz-api.api_key')] + $params)
            ->get($endpoint)
            ->throw();
    }
}
