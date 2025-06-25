<?php

namespace Rastik1584\LaravelMapyczApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array search(string $query, array $options = [])
 * @method static array geocode(string $address)
 * 
 * @see \Rastik1584\LaravelMapyczApi\MapyczApi
 */
class MapyczApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mapycz-api';
    }
}