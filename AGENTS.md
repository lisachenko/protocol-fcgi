# Agent Guidelines for protocol-fcgi

Guidance for AI coding agents (and humans) working on this repository.

## Project Overview

`lisachenko/protocol-fcgi` is a zero-dependency, object-oriented implementation of the
[FastCGI 1.0 binary protocol](https://fast-cgi.github.io/spec) for PHP. It provides
low-level building blocks (records and a frame parser) for writing FastCGI clients
(e.g. talking to `php-fpm` directly) and FastCGI servers/daemons.

- Package name: `lisachenko/protocol-fcgi`
- PSR-4 namespace: `Lisachenko\Protocol\` → `src/`
- Runtime dependencies: none (only PHP itself)

## Architecture Map

| Path | Purpose |
|------|---------|
| `src/FCGI.php` | Protocol-level constants (header length/format, version, flags) |
| `src/FCGI/Record.php` | Base record: header pack/unpack, payload handling, `__toString()` emits wire bytes |
| `src/FCGI/FrameParser.php` | Turns a binary buffer into typed `Record` instances (`hasFrame()` / `parseFrame()`) |
| `src/FCGI/Record/*.php` | One class per FCGI record type (BeginRequest, EndRequest, Params, Stdin, Stdout, Stderr, Data, AbortRequest, GetValues, GetValuesResult, UnknownType) |
| `tests/` | PHPUnit tests mirroring `src/`, driven by hex fixtures captured from real traffic |

## Commands

```bash
composer install          # install dev dependencies
composer test             # run the PHPUnit test suite (vendor/bin/phpunit)
composer phpstan          # run PHPStan static analysis (vendor/bin/phpstan analyse)
composer check            # run both
```

Both PHPStan and PHPUnit must pass before pushing.

## Conventions

- **Conventional Commits** for every commit message: `feat:`, `fix:`, `chore:`, `docs:`,
  `refactor:`, `perf:`, `ci:`, `test:`. Use `!` (e.g. `feat!:`) for breaking changes.
- **Branch naming**: `<type>/<kebab-description>`, e.g. `feat/websocket-records`,
  `fix/padding-overflow`, `docs/update-readme`.
- Every PHP file starts with `declare(strict_types=1);`.
- Static analysis runs at the level configured in `phpstan.neon` over both `src/` and
  `tests/` — do not add suppressions or baselines; fix the root cause instead.
- Tests are plain PHPUnit `TestCase` classes with `testXxx(): void` methods.
- Tests intentionally share the production namespace (`Lisachenko\Protocol\...`) via the
  `autoload-dev` mapping — do not "fix" this.

## The Wire Format Is Sacred

This library implements a binary network protocol. Byte layouts must never change:

- Every change to a record class needs a pack/unpack **round-trip test** asserting
  byte-exact output against a captured hex fixture (see the existing
  `$rawMessage` fixtures in `tests/`, taken from Wireshark captures of real
  FastCGI traffic).
- Records are padded to an 8-byte boundary; padding is computed in
  `Record::setContentData()`.
- `FrameParser::parseFrame()` **mutates the given buffer by reference**, consuming the
  parsed frame.
- Unpacking creates records **without invoking constructors** (hydration), so the raw
  wire bytes are preserved exactly even for legal-but-non-canonical peer encodings.
- `Params` name-value pairs use variable-length encoding: 1-byte lengths for < 128,
  4-byte lengths (high bit set) otherwise.

## Pull Requests

- Keep PRs focused: one concern per PR.
- Update tests alongside code; update `CHANGELOG.md` for user-visible changes.
- CI (GitHub Actions) runs PHPUnit across the supported PHP matrix plus PHPStan —
  a green build is required.
