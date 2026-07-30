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
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\RecordType;

/**
 * Record for unknown queries
 *
 * The set of management record types is likely to grow in future versions of this protocol.
 * To provide for this evolution, the protocol includes the FCGI_UNKNOWN_TYPE management record.
 * When an application receives a management record whose type T it does not understand, the application responds
 * with {FCGI_UNKNOWN_TYPE, 0, {T}}.
 */
final class UnknownType extends Record
{
    /**
     * @param int    $unrecognizedType Type of the unrecognized management record
     * @param string $reserved1        Reserved data, 7 bytes maximum
     */
    public function __construct(
        protected int $unrecognizedType,
        protected string $reserved1 = '',
    ) {
        $this->type = RecordType::UnknownType;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns the unrecognized type
     */
    public function getUnrecognizedType(): int
    {
        return $this->unrecognizedType;
    }

    protected function unpackPayload(string $binaryData): void
    {
        /** @var false|array{type: int, reserved: string} $payload */
        $payload = unpack("Ctype/a7reserved", $binaryData);
        if ($payload === false) {
            throw new ProtocolException('Can not unpack the FCGI_UnknownTypeBody');
        }

        $this->unrecognizedType = $payload['type'];
        $this->reserved1        = $payload['reserved'];
    }

    protected function packPayload(): string
    {
        return pack(
            "Ca7",
            $this->unrecognizedType,
            $this->reserved1
        );
    }
}
