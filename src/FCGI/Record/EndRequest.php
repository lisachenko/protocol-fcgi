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

use Lisachenko\Protocol\FCGI\ProtocolException;
use Lisachenko\Protocol\FCGI\ProtocolStatus;
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\RecordType;

/**
 * The application sends a FCGI_END_REQUEST record to terminate a request, either because the application
 * has processed the request or because the application has rejected the request.
 */
final class EndRequest extends Record
{
    /**
     * @param ProtocolStatus $protocolStatus The protocol-level status code
     * @param int            $appStatus      The application-level status code, each role documents its usage
     * @param string         $reserved1      Reserved data, 3 bytes maximum
     */
    public function __construct(
        protected ProtocolStatus $protocolStatus = ProtocolStatus::RequestComplete,
        protected int $appStatus = 0,
        protected string $reserved1 = '',
    ) {
        $this->type = RecordType::EndRequest;
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
     * Returns the protocol-level status code
     */
    public function getProtocolStatus(): ProtocolStatus
    {
        return $this->protocolStatus;
    }

    protected function unpackPayload(string $binaryData): void
    {
        /** @var false|array{appStatus: int, protocolStatus: int, reserved: string} $payload */
        $payload = unpack("NappStatus/CprotocolStatus/a3reserved", $binaryData);
        if ($payload === false) {
            throw new ProtocolException('Can not unpack the FCGI_EndRequestBody');
        }

        $this->appStatus      = $payload['appStatus'];
        $this->protocolStatus = ProtocolStatus::tryFrom($payload['protocolStatus'])
            ?? throw new ProtocolException("Invalid FastCGI protocol status {$payload['protocolStatus']} received");
        $this->reserved1      = $payload['reserved'];
    }

    protected function packPayload(): string
    {
        return pack(
            "NCa3",
            $this->appStatus,
            $this->protocolStatus->value,
            $this->reserved1
        );
    }
}
