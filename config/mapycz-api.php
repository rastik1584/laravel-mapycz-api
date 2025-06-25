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
];
