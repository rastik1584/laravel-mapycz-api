<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mapy.cz API Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for the Mapy.cz API integration.
    |
    */

    // API Key (if required by Mapy.cz)
    'api_key' => env('MAPYCZ_API_KEY', env('MAPYCZ_API_KEY', '')),

    // Base URL for the Mapy.cz API
    'base_url' => env('MAPYCZ_API_BASE_URL', 'https://api.mapy.cz/v1/'),

    // Default timeout for API requests in seconds
    'timeout' => env('MAPYCZ_API_TIMEOUT', 30),

    // Default language for API responses
    'language' => env('MAPYCZ_API_LANGUAGE', 'cs'),

    // Cache configuration
    'cache' => [
        'enabled' => env('MAPYCZ_API_CACHE_ENABLED', true),
        'ttl' => env('MAPYCZ_API_CACHE_TTL', 3600), // Time to live in seconds
    ],

    // SSL verification (disable for local development if having certificate issues)
    'verify_ssl' => env('MAPYCZ_API_VERIFY_SSL', true),

    'aliases' => [
        'MapyczApi' => \Rastik1584\LaravelMapyczApi\Facades\MapyczApi::class,
    ],

    // Defaults and allowed params
    'allowed_params' => [
        'routeType' => ['car_fast', 'car_fast_traffic', 'car_short', 'foot_fast', 'bike_road', 'bike_mountain'],
        'lang' => ['cs', 'de', 'el', 'en', 'es', 'fr', 'it', 'nl', 'pl', 'pt', 'ru', 'sk', 'tr', 'uk'],
        'mapSets' => ['basic', 'outdoor', 'aerial', 'winter', 'aerial-names-overlay'],
        'imageFormats' => ['png', 'jpg', 'webp', 'gif'],
        'routingFormats' => ['geojson', 'polyline', 'polyline6'],

    ]
];
