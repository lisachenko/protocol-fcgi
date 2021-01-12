<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record;

/**
 * The application sends a FCGI_END_REQUEST record to terminate a request, either because the application
 * has processed the request or because the application has rejected the request.
 */
class EndRequest extends Record
{
    /**
     * The appStatus component is an application-level status code. Each role documents its usage of appStatus.
     */
    protected int $appStatus = 0;

    /**
     * The protocolStatus component is a protocol-level status code.
     *
     * The possible protocolStatus values are:
     *   FCGI_REQUEST_COMPLETE: normal end of request.
     *   FCGI_CANT_MPX_CONN: rejecting a new request.
     *      This happens when a Web server sends concurrent requests over one connection to an application that is
     *      designed to process one request at a time per connection.
     *   FCGI_OVERLOADED: rejecting a new request.
     *      This happens when the application runs out of some resource, e.g. database connections.
     *   FCGI_UNKNOWN_ROLE: rejecting a new request.
     *      This happens when the Web server has specified a role that is unknown to the application.
     */
    protected int $protocolStatus = FCGI::REQUEST_COMPLETE;

    /**
     * Reserved data, 3 bytes maximum
     */
    protected string $reserved1;

    public function __construct(int $protocolStatus = FCGI::REQUEST_COMPLETE, int $appStatus = 0, string $reserved = '')
    {
        $this->type           = FCGI::END_REQUEST;
        $this->protocolStatus = $protocolStatus;
        $this->appStatus      = $appStatus;
        $this->reserved1      = $reserved;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns app status
     *
     * The appStatus component is an application-level status code. Each role documents its usage of appStatus.
     */
    public function getAppStatus(): int
    {
        return $this->appStatus;
    }

    /**
     * Returns the protocol status
     *
     * The possible protocolStatus values are:
     *   FCGI_REQUEST_COMPLETE: normal end of request.
     *   FCGI_CANT_MPX_CONN: rejecting a new request.
     *      This happens when a Web server sends concurrent requests over one connection to an application that is
     *      designed to process one request at a time per connection.
     *   FCGI_OVERLOADED: rejecting a new request.
     *      This happens when the application runs out of some resource, e.g. database connections.
     *   FCGI_UNKNOWN_ROLE: rejecting a new request.
     *      This happens when the Web server has specified a role that is unknown to the application.
     */
    public function getProtocolStatus(): int
    {
        return $this->protocolStatus;
    }

    /**
     * {@inheritdoc}
     */
    protected static function unpackPayload($self, string $binaryData): void
    {
        assert($self instanceof self);

        /** @phpstan-var false|array{appStatus: int, protocolStatus: int, reserved: string} */
        $payload = unpack("NappStatus/CprotocolStatus/a3reserved", $binaryData);
        if ($payload === false) {
            throw new \RuntimeException('Can not unpack data from the binary buffer');
        }
        [
            $self->appStatus,
            $self->protocolStatus,
            $self->reserved1
        ] = array_values($payload);
    }

    /**
     * {@inheritdoc}
     */
    protected function packPayload(): string
    {
        return pack(
            "NCa3",
            $this->appStatus,
            $this->protocolStatus,
            $this->reserved1
        );
    }
}
