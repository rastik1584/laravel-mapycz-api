# !!! Work in progress !!!
# Laravel Mapy.cz API

### Support Laravel version 10 and higher

## About this package

This package provides Laravel integration with the Mapy.cz REST API.

> ⚠️ Disclaimer: This package relies on the external [Mapy.cz API](https://api.mapy.cz). Usage of this API is subject to the terms and conditions of Seznam.cz, available at [https://napoveda.seznam.cz/en/mapy/basic-information/api/](https://napoveda.seznam.cz/en/mapy/basic-information/api/).

Users of this package are responsible for:

- obtaining their own API key from Seznam.cz,
- complying with Mapy.cz’s terms of service,
- ensuring proper attribution (if required).

This package does not provide any map data or tile services.

[//]: # (A Laravel package for integrating with the Mapy.cz API.)

[//]: # ()
[//]: # (## Installation)

[//]: # ()
[//]: # (You can install the package via composer:)

[//]: # ()
[//]: # (```bash)

[//]: # (composer require rastik1584/laravel-mapycz-api)

[//]: # (```)

[//]: # ()
[//]: # (The package will automatically register its service provider.)

[//]: # ()
[//]: # (## Configuration)

[//]: # ()
[//]: # (You can publish the configuration file using:)

[//]: # ()
[//]: # (```bash)

[//]: # (php artisan vendor:publish --provider="Rastik1584\LaravelMapyczApi\MapyczApiServiceProvider" --tag="config")

[//]: # (```)

[//]: # ()
[//]: # (This will create a `config/mapycz-api.php` file in your app where you can modify the package configuration.)

[//]: # ()
[//]: # (```php)

[//]: # (return [)

[//]: # (    // API Key &#40;if required by Mapy.cz&#41;)

[//]: # (    'api_key' => env&#40;'MAPYCZ_API_KEY', ''&#41;,)

[//]: # ()
[//]: # (    // Base URL for the Mapy.cz API)

[//]: # (    'base_url' => env&#40;'MAPYCZ_API_BASE_URL', 'https://api.mapy.cz'&#41;,)

[//]: # ()
[//]: # (    // Default timeout for API requests in seconds)

[//]: # (    'timeout' => env&#40;'MAPYCZ_API_TIMEOUT', 30&#41;,)

[//]: # ()
[//]: # (    // Default language for API responses)

[//]: # (    'language' => env&#40;'MAPYCZ_API_LANGUAGE', 'cs'&#41;,)

[//]: # ()
[//]: # (    // Cache configuration)

[//]: # (    'cache' => [)

[//]: # (        'enabled' => env&#40;'MAPYCZ_API_CACHE_ENABLED', true&#41;,)

[//]: # (        'ttl' => env&#40;'MAPYCZ_API_CACHE_TTL', 3600&#41;, // Time to live in seconds)

[//]: # (    ],)

[//]: # (];)

[//]: # (```)

[//]: # ()
[//]: # (## Usage)

[//]: # ()
[//]: # (### Using the Facade)

[//]: # ()
[//]: # (```php)

[//]: # (use Rastik1584\LaravelMapyczApi\Facades\MapyczApi;)

[//]: # ()
[//]: # (// Search for locations)

[//]: # ($results = MapyczApi::search&#40;'Prague'&#41;;)

[//]: # ()
[//]: # (// Geocode an address)

[//]: # ($geocodeResults = MapyczApi::geocode&#40;'Václavské náměstí, Praha'&#41;;)

[//]: # (```)

[//]: # ()
[//]: # (### Using Dependency Injection)

[//]: # ()
[//]: # (```php)

[//]: # (use Rastik1584\LaravelMapyczApi\MapyczApi;)

[//]: # ()
[//]: # (class LocationController extends Controller)

[//]: # ({)

[//]: # (    protected $mapyczApi;)

[//]: # ()
[//]: # (    public function __construct&#40;MapyczApi $mapyczApi&#41;)

[//]: # (    {)

[//]: # (        $this->mapyczApi = $mapyczApi;)

[//]: # (    })

[//]: # ()
[//]: # (    public function search&#40;Request $request&#41;)

[//]: # (    {)

[//]: # (        $results = $this->mapyczApi->search&#40;$request->input&#40;'query'&#41;&#41;;)

[//]: # ()
[//]: # (        return response&#40;&#41;->json&#40;$results&#41;;)

[//]: # (    })

[//]: # (})

[//]: # (```)

[//]: # ()
[//]: # (## Available Methods)

[//]: # ()
[//]: # (### search&#40;string $query, array $options = []&#41;)

[//]: # ()
[//]: # (Search for locations using the Mapy.cz API.)

[//]: # ()
[//]: # (```php)

[//]: # ($results = MapyczApi::search&#40;'Prague', [)

[//]: # (    'limit' => 10,)

[//]: # (    // Additional options...)

[//]: # (]&#41;;)

[//]: # (```)

[//]: # ()
[//]: # (### geocode&#40;string $address&#41;)

[//]: # ()
[//]: # (Get geocoding information for an address.)

[//]: # ()
[//]: # (```php)

[//]: # ($geocodeResults = MapyczApi::geocode&#40;'Václavské náměstí, Praha'&#41;;)

[//]: # (```)

[//]: # ()
[//]: # (### functions&#40;string $function, array $params = []&#41;)

[//]: # ()
[//]: # (Access the functions &#40;funkce&#41; endpoint of the Mapy.cz API.)

[//]: # ()
[//]: # (```php)

[//]: # (// Example of calling a specific function from the Mapy.cz API)

[//]: # ($results = MapyczApi::functions&#40;'route', [)

[//]: # (    'start' => '50.0755,14.4378', // Prague)

[//]: # (    'end' => '49.1951,16.6068',   // Brno)

[//]: # (    'mode' => 'car',)

[//]: # (]&#41;;)

[//]: # (```)

For more information about available functions and parameters, see the [Mapy.cz API documentation](https://developer.mapy.com/cs/rest-api/funkce/).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
