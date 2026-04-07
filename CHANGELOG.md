# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2026-04-07

### Added

- Complete coverage of all `13/13` currently documented Mapy.com REST endpoints.
- Elevation API support.
- Timezone API support for timezone list, timezone detail, and timezone lookup by coordinates.
- Typed response DTOs for major JSON endpoints.
- Dedicated package exceptions for invalid DTO payloads, missing API key, and remote API failures.
- Request caching support driven by package config.
- PHPStan and Pint based quality tooling.
- CI matrix for Laravel 10, 11, 12, and 13.
- Release checklist and upgrade guide.

### Changed

- Public API now exposes all supported services through the root package API and facade.
- Routing, matrix, geocoding, static map, tile, elevation, and timezone DTO serialization was aligned with the official Mapy.com API documentation.
- README was rewritten to reflect the actual API surface and to clearly state that this is an unofficial package.
- Package compatibility now includes Laravel 13 on PHP 8.3+.
- Test suite was upgraded to run on PHPUnit 10 through 12.

### Fixed

- Reverse geocoding now targets the correct `v1/rgeocode` endpoint.
- Matrix routing serialization now handles `starts` and `ends` correctly.
- Static map markers and shapes now serialize consistently.
- Shared HTTP client now applies timeout, SSL verification, error wrapping, and query normalization consistently.

### Removed

- Removed the legacy `functions()` passthrough from the public API.

### Breaking changes

- `functions()` no longer exists in the public API.
- Consumers should migrate to explicit endpoint methods such as `suggest()`, `route()`, `matrixM()`, `elevations()`, and timezone methods.

## [1.0.1] - Previous release

- Existing public release on Packagist before the 2.0.0 API cleanup and full endpoint coverage work.
