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
 * Values for the protocolStatus component of FCGI_EndRequestBody.
 */
enum ProtocolStatus: int
{
    /**
     * Normal end of request.
     */
    case RequestComplete = 0;

    /**
     * Rejecting a new request: the Web server sent concurrent requests over one connection
     * to an application that is designed to process one request at a time per connection.
     */
    case CantMultiplexConnection = 1;

    /**
     * Rejecting a new request: the application ran out of some resource, e.g. database connections.
     */
    case Overloaded = 2;

    /**
     * Rejecting a new request: the Web server specified a role that is unknown to the application.
     */
    case UnknownRole = 3;
}
