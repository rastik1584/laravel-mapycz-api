# Upgrade Guide

## Upgrading from 1.0.1 to 2.0.0

Version `2.0.0` is a major release because it removes the legacy `functions()` method from the public API.

## Breaking changes

### Removed `functions()`

If your code used:

```php
MapyczApi::functions(...);
app(MapyczApi::class)->functions(...);
```

you must migrate to explicit supported endpoint methods.

Typical replacements:

- use `suggest()` for autocomplete and generic place search
- use `geocode()` for forward geocoding
- use `reverse()` for reverse geocoding
- use `route()` and `routeResult()` for route planning
- use `matrixM()` and `matrixResult()` for matrix routing
- use `staticImage()` and `panoramaImage()` for static imagery
- use `tile()` and `tileJson()` for tiles
- use `elevations()` for elevation lookups
- use `listTimezones()`, `timezoneInfo()`, and `timezoneByCoordinates()` for timezone operations

## Compatibility

Version `2.0.0` supports:

- Laravel 10 on PHP 8.1+
- Laravel 11 on PHP 8.2+
- Laravel 12 on PHP 8.2+
- Laravel 13 on PHP 8.3+

## Notes

- The package now covers all currently documented Mapy.com REST endpoints.
- README now explicitly states that this is an unofficial package and that there is no affiliation with Mapy.com or Seznam.cz.
