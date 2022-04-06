# Changelog

All notable changes to this project will be documented in this file.

Using format defined on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v1.1.0] - 2022-04-06
### Adds
- support for PHP 8+
### Removes
- support for PHP 7.x

## [v1.0.0] - 2022-02-04
### Added
- [GitHub workflows] for continuous integration
- [Packagist.org] integration
- [Scrutinizer CI] integration
### Changed
- [README](README.md) file usage section

## [v0.1.0] - 2022-02-01
### Added
- Trackable models: `Metrid`, `Event`, `PageView`, `Request`, `Dependency` and `ExceptionTrackable`
- PSR-3 `TelemetryClient` implementation
- Message interpolation with context values as per [PSR-3 specification]

[Unreleased]: https://github.com/slickframework/telemetry/compare/v1.1.0...HEAD
[v1.1.0]: https://github.com/slickframework/telemetry/compare/v1.0.0...v1.1.0
[v1.0.0]: https://github.com/slickframework/telemetry/compare/v0.1.0...v1.0.0
[v0.1.0]: https://github.com/slickframework/telemetry/compare/f802013b8194fcbb7ffbeaf23f61882b7c338b10...v0.1.0

[PSR-3 specification]: https://www.php-fig.org/psr/psr-3
[Packagist.org]: https://packagist.org/packages/slick/telemetry
[Scrutinizer CI]: https://scrutinizer-ci.com/g/slickframework/telemetry/?branch=master
[GitHub workflows]: https://github.com/slickframework/telemetry/actions
