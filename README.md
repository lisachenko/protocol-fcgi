# PHP FastCGI Protocol

[![CI](https://github.com/lisachenko/protocol-fcgi/actions/workflows/ci.yml/badge.svg)](https://github.com/lisachenko/protocol-fcgi/actions/workflows/ci.yml)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg?style=flat&link=https%3A%2F%2Fphpstan.org%2Fuser-guide%2Frule-levels)
[![Latest Version](https://img.shields.io/packagist/v/lisachenko/protocol-fcgi.svg)](https://packagist.org/packages/lisachenko/protocol-fcgi)
[![Total Downloads](https://img.shields.io/packagist/dt/lisachenko/protocol-fcgi.svg)](https://packagist.org/packages/lisachenko/protocol-fcgi)
[![Daily Downloads](https://img.shields.io/packagist/dd/lisachenko/protocol-fcgi.svg)](https://packagist.org/packages/lisachenko/protocol-fcgi)
[![Minimum PHP Version](https://img.shields.io/packagist/dependency-v/lisachenko/protocol-fcgi/php.svg?colorB=8892BF)](https://www.php.net/supported-versions.php)
[![License](https://img.shields.io/packagist/l/lisachenko/protocol-fcgi.svg)](https://packagist.org/packages/lisachenko/protocol-fcgi)

A **zero-dependency, object-oriented implementation of the FastCGI 1.0 binary protocol** for PHP.

FastCGI is the battle-tested protocol that web servers like nginx, Apache and Caddy use to
talk to `php-fpm` — billions of requests flow through it every day. This library gives you
the protocol itself as a clean, strictly-typed PHP API, so you can build your own
high-performance FastCGI **clients** (talk to `php-fpm` directly, no web server in between)
and **servers** (long-running PHP daemons that nginx can speak to natively).

## ✨ Key Features

- 📦 **Complete protocol coverage** — all 11 FastCGI record types, including the
  management records (`GET_VALUES`, `GET_VALUES_RESULT`, `UNKNOWN_TYPE`)
- 🌊 **Streaming frame parser** — feed partial socket reads into
  `FrameParser::hasFrame()` / `parseFrame()` and get fully-typed record objects out as
  soon as they are complete
- 🔄 **Byte-exact round-tripping** — every record packs back to the exact wire bytes it
  was parsed from; the test suite is pinned to hex fixtures captured from real traffic
- 📏 **Automatic 8-byte padding** — content alignment is handled for you, as the spec
  recommends
- 🏷️ **Full name-value pair encoding** — including the 4-byte long form for names and
  values over 127 bytes
- 🪶 **Zero runtime dependencies** — pure PHP, nothing but the language itself
- 🔒 **Strict types + PHPStan at the maximum level** — the whole codebase (tests
  included) passes static analysis at the strictest setting

## Requirements

- PHP >= 8.4

## Installation

```bash
composer require lisachenko/protocol-fcgi
```

## Usage

The library implements both sides of the wire: use it to *send* FastCGI requests as a
client, or to *receive* and answer them as a server. The full protocol specification is
available at [fast-cgi.github.io/spec](https://fast-cgi.github.io/spec).

### FastCGI client: query php-fpm directly

```php
<?php

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\FrameParser;
use Lisachenko\Protocol\FCGI\Record\BeginRequest;
use Lisachenko\Protocol\FCGI\Record\EndRequest;
use Lisachenko\Protocol\FCGI\Record\Params;
use Lisachenko\Protocol\FCGI\Record\Stdin;
use Lisachenko\Protocol\FCGI\Record\Stdout;

include 'vendor/autoload.php';

// Connect to the local php-fpm daemon directly
$phpSocket = fsockopen('127.0.0.1', 9001, $errorNumber, $errorString);

// Prepare the request: begin, pass parameters, then close the input stream.
// Empty Params and Stdin records mark the end of the corresponding stream.
$packet  = '';
$packet .= new BeginRequest(FCGI::RESPONDER);
$packet .= new Params(['SCRIPT_FILENAME' => '/var/www/some_file.php']);
$packet .= new Params([]);
$packet .= new Stdin('');

fwrite($phpSocket, $packet);

// Read the response incrementally: the parser consumes complete frames
// from the buffer and leaves partial ones for the next read.
$buffer = '';
while ($partialData = fread($phpSocket, 4096)) {
    $buffer .= $partialData;
    while (FrameParser::hasFrame($buffer)) {
        $record = FrameParser::parseFrame($buffer);
        if ($record instanceof Stdout) {
            echo $record->getContentData();
        }
        if ($record instanceof EndRequest) {
            break 2; // response is complete
        }
    }
}

fclose($phpSocket);
```

### FastCGI server: accept requests from a web server

```php
<?php

use Lisachenko\Protocol\FCGI\FrameParser;

include 'vendor/autoload.php';

$server = stream_socket_server('tcp://127.0.0.1:9001', $errorNumber, $errorString);

// Accept one connection and parse everything the web server sends
$socket = stream_socket_accept($server);

$buffer = '';
while ($partialData = fread($socket, 4096)) {
    $buffer .= $partialData;
    while (FrameParser::hasFrame($buffer)) {
        $record = FrameParser::parseFrame($buffer);
        var_dump($record); // BeginRequest, Params, Stdin, ...
    }
}

// Answering (Stdout + EndRequest records) is up to your application

fclose($socket);
fclose($server);
```

## Quality

```bash
composer test      # PHPUnit test suite
composer phpstan   # PHPStan static analysis
composer check     # both
```

## License

Released under the [MIT license](LICENSE).
