# Release Notes 2.0.0

## Summary

This is the first major release of the package after the initial public `1.0.1` release.

`2.0.0` completes the package API against the currently documented Mapy.com REST surface and removes the old `functions()` passthrough from the public API.

## Highlights

- `13/13` documented REST endpoints implemented
- elevation and timezone support added
- typed response DTOs added
- Laravel 13 support added on PHP 8.3+
- PHPStan, Pint, CI matrix, and release checklist added
- README clarified that the package is unofficial and not affiliated with Mapy.com or Seznam.cz

## Breaking change

- Removed `functions()` from the public API

## Verification

- `composer qa`
- `composer validate --no-check-publish`

## Recommended tag message

```text
2.0.0

Major release:
- full documented Mapy.com REST API coverage
- removed legacy functions() passthrough
- added Laravel 13 support
- added release hardening and QA tooling
```
