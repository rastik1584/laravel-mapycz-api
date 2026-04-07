<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Rastik1584\LaravelMapyczApi\Exceptions\MapyczApiRequestException;
use Rastik1584\LaravelMapyczApi\Exceptions\MissingApiKeyException;

class MapyczApiClient
{
    /**
     * @throws MapyczApiRequestException
     */
    public function get(string $endpoint, array $params = []): Response
    {
        $apiKey = (string) config('mapycz-api.api_key');

        if ($apiKey === '') {
            throw new MissingApiKeyException('API key is not set.');
        }

        $params = array_filter(
            $params,
            static fn ($value): bool => $value !== null && $value !== []
        );

        $payload = ['apikey' => $apiKey] + $params;

        if ($this->cacheEnabled()) {
            $cached = Cache::remember(
                $this->cacheKey($endpoint, $payload),
                now()->addSeconds((int) config('mapycz-api.cache.ttl', 3600)),
                fn (): array => $this->send($endpoint, $payload)
            );

            return $this->restoreResponse($cached);
        }

        return $this->restoreResponse($this->send($endpoint, $payload));
    }

    protected function newRequest(): PendingRequest
    {
        $request = Http::baseUrl(rtrim((string) config('mapycz-api.base_url', 'https://api.mapy.cz/v1/'), '/').'/')
            ->timeout((int) config('mapycz-api.timeout', 30));

        if (! config('mapycz-api.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    /**
     * @return array{status:int, headers:array<string, array<int, string>>, body:string}
     *
     * @throws MapyczApiRequestException
     */
    protected function send(string $endpoint, array $params): array
    {
        try {
            $query = $this->buildQuery($params);
            $response = $this->newRequest()
                ->get(ltrim($endpoint, '/').($query !== '' ? '?'.$query : ''))
                ->throw();
        } catch (RequestException $exception) {
            throw MapyczApiRequestException::fromRequestException($exception);
        }

        return [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
        ];
    }

    /**
     * @param  array{status:int, headers:array<string, array<int, string>>, body:string}  $cached
     */
    protected function restoreResponse(array $cached): Response
    {
        return new Response(
            new Psr7Response($cached['status'], $cached['headers'], $cached['body'])
        );
    }

    protected function cacheEnabled(): bool
    {
        return (bool) config('mapycz-api.cache.enabled', false);
    }

    protected function cacheKey(string $endpoint, array $params): string
    {
        ksort($params);

        return 'mapycz-api:'.md5(ltrim($endpoint, '/').'|'.serialize($params));
    }

    protected function buildQuery(array $params): string
    {
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $parts[] = rawurlencode((string) $key).'='.rawurlencode($this->normalizeQueryValue($item));
                }

                continue;
            }

            $parts[] = rawurlencode((string) $key).'='.rawurlencode($this->normalizeQueryValue($value));
        }

        return implode('&', $parts);
    }

    protected function normalizeQueryValue(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? '1' : '0',
            default => (string) $value,
        };
    }
}
