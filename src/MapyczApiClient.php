<?php

namespace Rastik1584\LaravelMapyczApi;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

        // Disable SSL verification if configured
        if (!config('mapycz-api.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }
        // Todo: remove after publishing
//        $baseUrl = config('mapycz-api.base_url');
//        $queryParams = ['apikey' => config('mapycz-api.api_key')] + $params;
//
//// Vygenerovaná URL:
//        $fullUrl = $baseUrl . $endpoint . '?' . http_build_query($queryParams);
//        dd($fullUrl, $params);

        return $request
            ->withQueryParameters(['apikey' => config('mapycz-api.api_key')] + $params)
            ->get($endpoint)
            ->throw();
    }
}
