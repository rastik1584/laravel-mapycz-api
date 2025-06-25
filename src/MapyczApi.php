<?php

namespace Rastik1584\LaravelMapyczApi;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class MapyczApi
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new MapyczApi instance.
     *
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config['base_url'],
            'timeout' => $config['timeout'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Search for locations using the Mapy.cz API.
     *
     * @param string $query
     * @param array $options
     * @return array
     */
    public function search(string $query, array $options = [])
    {
        $cacheKey = 'mapycz_search_' . md5($query . serialize($options));

        if ($this->config['cache']['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = array_merge([
            'query' => $query,
            'lang' => $this->config['language'],
        ], $options);

        $response = $this->makeRequest('GET', '/v1/suggest', [
            'query' => $params,
        ]);

        if ($this->config['cache']['enabled']) {
            Cache::put($cacheKey, $response, $this->config['cache']['ttl']);
        }

        return $response;
    }

    /**
     * Get geocoding information for an address.
     *
     * @param string $address
     * @return array
     */
    public function geocode(string $address)
    {
        $cacheKey = 'mapycz_geocode_' . md5($address);

        if ($this->config['cache']['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->makeRequest('GET', '/v1/geocode', [
            'query' => [
                'query' => $address,
                'lang' => $this->config['language'],
            ],
        ]);

        if ($this->config['cache']['enabled']) {
            Cache::put($cacheKey, $response, $this->config['cache']['ttl']);
        }

        return $response;
    }

    /**
     * Access the functions (funkce) endpoint of the Mapy.cz API.
     *
     * @param string $function The function name to call
     * @param array $params Additional parameters for the function
     * @return array
     */
    public function functions(string $function, array $params = [])
    {
        $cacheKey = 'mapycz_functions_' . md5($function . serialize($params));

        if ($this->config['cache']['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $queryParams = array_merge([
            'lang' => $this->config['language'],
        ], $params);

        $response = $this->makeRequest('GET', '/v1/funkce/' . $function, [
            'query' => $queryParams,
        ]);

        if ($this->config['cache']['enabled']) {
            Cache::put($cacheKey, $response, $this->config['cache']['ttl']);
        }

        return $response;
    }

    /**
     * Make a request to the Mapy.cz API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array
     */
    protected function makeRequest(string $method, string $endpoint, array $options = [])
    {
        // Add API key to the request if it's set
        if (!empty($this->config['api_key'])) {
            $options['headers'] = $options['headers'] ?? [];
            $options['headers']['X-Api-Key'] = $this->config['api_key'];
        }

        $response = $this->client->request($method, $endpoint, $options);

        return json_decode($response->getBody()->getContents(), true);
    }
}
