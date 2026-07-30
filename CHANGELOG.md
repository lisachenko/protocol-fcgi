# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [4.0.0] - 2026-07-30

A full modernization of the library for PHP 8.4.

### Added

- `Lisachenko\Protocol\FCGI\RecordType` — int-backed enum of all 11 FastCGI record
  types, with `recordClass()` mapping each type to its record class.
- `Lisachenko\Protocol\FCGI\Role` — int-backed enum of the FCGI_BeginRequestBody
  roles (`Responder`, `Authorizer`, `Filter`).
- `Lisachenko\Protocol\FCGI\ProtocolStatus` — int-backed enum of the
  FCGI_EndRequestBody protocol statuses (`RequestComplete`,
  `CantMultiplexConnection`, `Overloaded`, `UnknownRole`).
- `Lisachenko\Protocol\FCGI\ProtocolException` — dedicated exception (extends
  `RuntimeException`) for all protocol-level failures: malformed or truncated
  buffers, unknown record types, invalid roles or statuses on the wire.
- Composer scripts: `composer test`, `composer phpstan`, `composer check`.
- Agent/contributor guidelines in `AGENTS.md` (with `CLAUDE.md` pointing to it).
- Test coverage for protocol errors, names/values over 127 bytes (the 4-byte
  long length form) and the empty `FCGI_PARAMS` end-of-stream record.
- Dependabot updates for GitHub Actions.

### Changed

- **BC break**: PHP >= 8.4 is required.
- **BC break**: `Record::getType()`, `BeginRequest::getRole()` and
  `EndRequest::getProtocolStatus()` return enums instead of ints, and the
  `BeginRequest`/`EndRequest` constructors take enums.
- **BC break**: `BeginRequest` requires an explicit `Role` — the old default
  (`FCGI::UNKNOWN_ROLE`, whose value 3 silently meant *Filter*) is gone.
- **BC break**: `Record::unpackPayload()` is a protected **instance** method
  hydrating `$this`, replacing the static `unpackPayload($self, ...)` protocol
  (and fixing the `public` visibility inconsistency in `UnknownType`).
- **BC break**: record classes are `final` (except `Params`, which stays open for
  `GetValues`/`GetValuesResult`), and `FrameParser` is `final`.
- **BC break**: protocol failures throw `ProtocolException`; code catching
  `DomainException` for unknown record types must catch `ProtocolException`
  (broad `RuntimeException` catches keep working). Invalid roles and protocol
  statuses on the wire now fail fast instead of being accepted silently.
- Performance: `Record::unpack()` reuses a cached per-class `ReflectionClass`;
  `FrameParser::hasFrame()` unpacks only the two length fields it needs;
  `Params::unpackPayload()` parses with an integer cursor instead of making
  three O(n) buffer copies per name-value pair.
- Modern language surface: constructor property promotion, native `static`
  return types, throw expressions, explicit `Stringable`, strict enums-based
  validation — under PHPStan level max (was level 8) with PHPStan 2 and
  PHPUnit 12 (schema-migrated `phpunit.xml.dist`).
- CI consolidated into a single workflow (PHPUnit matrix on PHP 8.4/8.5 with
  lowest/highest dependencies, plus PHPStan) using maintained actions.
- README rewritten: working examples, key features, current badges.
- Composer branch alias is now `4.x-dev`.

### Removed

- Support for PHP < 8.4.
- The record type, role and protocol status int constants on the `FCGI` class
  (`FCGI::BEGIN_REQUEST` … `FCGI::UNKNOWN_TYPE`, `FCGI::RESPONDER`,
  `FCGI::AUTHORIZER`, `FCGI::FILTER`, `FCGI::REQUEST_COMPLETE`,
  `FCGI::CANT_MPX_CONN`, `FCGI::OVERLOADED`, `FCGI::UNKNOWN_ROLE`) — use the
  `RecordType`, `Role` and `ProtocolStatus` enums. `FCGI` keeps the structural
  constants (`HEADER_LEN`, `HEADER_FORMAT`, `VERSION_1`, `NULL_REQUEST_ID`,
  `KEEP_CONN`) and can no longer be instantiated.
- The legacy `phpunit.yml`/`phpstan.yml` workflows and the unpinned third-party
  `chindit/actions-phpstan` action.
- The unused `AUTOLOAD_PATH` constant from `phpunit.xml.dist`.

### Design notes

- `readonly` properties and property hooks were evaluated and deliberately not
  used: unpacking hydrates records without invoking constructors (to preserve
  the exact wire bytes), and the chainable `setRequestId()`/`setContentData()`
  setters are part of the documented usage pattern.

## [3.0.0] - 2021-01-12

- Minimum PHP version raised to 7.4, typed properties throughout.
- Record constructor arguments became required.
- PHPStan (level 8) static analysis; CI moved from Travis to GitHub Actions.

## [2.0.1] - 2021-01-12

- Allowed installation on PHP 8.

## [2.0.0] - 2018-09-04

- Version 2.0 of the protocol implementation.

## [1.1.1] - 2015-12-05

- Code quality and packaging improvements.

## [1.1.0] - 2015-10-23

- Code style clean-up and project metrics.

## [1.0.0] - 2015-09-08

- Initial release: object-oriented implementation of the FastCGI 1.0 binary
  protocol (records, frame parser).

[Unreleased]: https://github.com/lisachenko/protocol-fcgi/compare/4.0.0...HEAD
[4.0.0]: https://github.com/lisachenko/protocol-fcgi/compare/3.0.0...4.0.0
[3.0.0]: https://github.com/lisachenko/protocol-fcgi/compare/2.0.1...3.0.0
[2.0.1]: https://github.com/lisachenko/protocol-fcgi/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/lisachenko/protocol-fcgi/compare/1.1.1...2.0.0
[1.1.1]: https://github.com/lisachenko/protocol-fcgi/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/lisachenko/protocol-fcgi/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/lisachenko/protocol-fcgi/releases/tag/1.0.0
