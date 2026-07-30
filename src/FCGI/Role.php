<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI;

/**
 * Values for the role component of FCGI_BeginRequestBody.
 */
enum Role: int
{
    /**
     * Emulates the functionality of a CGI program: receives the request and produces the response.
     */
    case Responder = 1;

    /**
     * Makes an authorization decision for the request.
     */
    case Authorizer = 2;

    /**
     * Filters the extra data stream (FCGI_DATA) before it is returned to the Web server.
     */
    case Filter = 3;
}
