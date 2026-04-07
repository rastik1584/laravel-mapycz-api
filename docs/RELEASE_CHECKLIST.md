# Release Checklist

## Before tagging

- [ ] Run `composer validate --no-check-publish`
- [ ] Run `composer qa`
- [ ] Review `CHANGELOG.md`
- [ ] Review `UPGRADE.md`
- [ ] Confirm README matches the current public API
- [ ] Confirm the package disclaimer remains present in README
- [ ] Confirm all documented Mapy.com endpoints are covered
- [ ] Review breaking changes since the previous tag
- [ ] Update version and changelog if needed
- [ ] Commit the release-ready working tree before creating the tag
- [ ] Create the git tag on the release commit, not on a dirty working tree

## Compatibility matrix

- Laravel 10 on PHP 8.1+
- Laravel 11 on PHP 8.2+
- Laravel 12 on PHP 8.2+
- Laravel 13 on PHP 8.3+

## Release notes

Include:

- supported Laravel and PHP versions
- endpoint coverage
- notable fixes and breaking changes
- disclaimer that this is an unofficial package for Mapy.com
