Object-oriented implementation of FCGI Protocol for PHP
---------------------------

FastCGI is an open extension to CGI that provides high performance for all Internet applications without the penalties
of Web server APIs.

Many modern web-servers such as nginx, apache, lighthttpd, etc are communicating with PHP via FCGI. So, this protocol
is well known and used in many applications. More detailed information about the protocol is available here here:
http://www.fastcgi.com/devkit/doc/fcgi-spec.html

Usage
------------
This library can be used for implementing both client and server side of FCGI application. For example, nginx can
connect to the PHP FCGI daemon, or some library code can connect to the FPM as a FCGI client.

To install this library, just write

``` bash
$ composer require lisachenko/protocol-fcgi
```

After that you can use an API to parse/create FCGI requests and responses.

Simple FCGI-client:
```php
<?php

use Protocol\FCGI;
use Protocol\FCGI\FrameParser;
use Protocol\FCGI\Record;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;
use Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

// Let's connect to the local php-fpm daemon directly
$phpSocket = fsockopen('127.0.0.1', 9001, $errorNumber, $errorString);
$packet    = '';

// Prepare our sequence for querying PHP file
$packet .= new BeginRequest(FCGI::RESPONDER);;
$packet .= new Params(['SCRIPT_FILENAME' => '/var/www/some_file.php']);
$packet .= new Params();
$packet .= new Stdin();

fwrite($phpSocket, $packet);

$response = '';
while ($partialData = fread($phpSocket, 4096)) {
    $response .= $partialData;
    while (FrameParser::hasFrame($response)) {
        $record = FrameParser::parseFrame($response);
        var_dump($record);
    };
};

fclose($phpSocket);
```

To implement FCGI server, just create a socket and make request-response loop

```php

use Protocol\FCGI;
use Protocol\FCGI\FrameParser;
use Protocol\FCGI\Record;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;
use Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

$server = stream_socket_server("tcp://127.0.0.1:9001" , $errorNumber, $errorString);

// Just take the first one request and process it
$phpSocket = stream_socket_accept($server);

$response = '';
while ($partialData = fread($phpSocket, 4096)) {
    $response .= $partialData;
    while (FrameParser::hasFrame($response)) {
        $record = FrameParser::parseFrame($response);
        var_dump($record);
    };
};

// We don't respond correctly here, it's a task for your application

fclose($phpSocket);
fclose($server);
```
