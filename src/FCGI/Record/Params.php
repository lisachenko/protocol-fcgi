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
 * Params request record
 */
class Params extends Record
{
    /**
     * List of params
     *
     * NB: this property intentionally keeps its default value instead of being promoted
     * to the constructor: an empty FCGI_PARAMS record (the end-of-stream marker) is
     * hydrated from the wire without running the constructor or unpackPayload().
     *
     * @var array<string, string>
     */
    protected array $values = [];

    /**
     * Constructs a param request
     *
     * @param array<string, string> $values
     */
    public function __construct(array $values)
    {
        $this->type   = RecordType::Params;
        $this->values = $values;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns an associative list of parameters
     *
     * @return array<string, string>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    protected function unpackPayload(string $binaryData): void
    {
        $payloadLength = strlen($binaryData);
        $offset        = 0;
        while ($offset < $payloadLength) {
            [$nameLength, $offset]  = self::unpackLength($binaryData, $offset);
            [$valueLength, $offset] = self::unpackLength($binaryData, $offset);

            $name   = substr($binaryData, $offset, $nameLength);
            $value  = substr($binaryData, $offset + $nameLength, $valueLength);
            $offset += $nameLength + $valueLength;

            $this->values[$name] = $value;
        }
    }

    /**
     * Reads one FCGI_NameValuePair length field at the given offset.
     *
     * Lengths below 128 are encoded in a single byte; longer ones use four bytes
     * with the top bit set.
     *
     * @return array{int, int} The decoded length and the offset right after it
     */
    private static function unpackLength(string $binaryData, int $offset): array
    {
        if (!isset($binaryData[$offset])) {
            throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
        }

        $firstByte = ord($binaryData[$offset]);
        if ($firstByte >> 7 === 0) {
            return [$firstByte, $offset + 1];
        }

        /** @var false|array{length: int} $payload */
        $payload = unpack('Nlength', $binaryData, $offset);
        if ($payload === false) {
            throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
        }

        return [$payload['length'] & 0x7fffffff, $offset + 4];
    }

    protected function packPayload(): string
    {
        $payload = '';
        foreach ($this->values as $nameData => $valueData) {
            $nameLength  = strlen($nameData);
            $valueLength = strlen($valueData);
            $isLongName  = $nameLength > 127;
            $isLongValue = $valueLength > 127;

            $format = ($isLongName ? 'N' : 'C')
                . ($isLongValue ? 'N' : 'C')
                . "a{$nameLength}a{$valueLength}";

            $payload .= pack(
                $format,
                $isLongName ? ($nameLength | 0x80000000) : $nameLength,
                $isLongValue ? ($valueLength | 0x80000000) : $valueLength,
                $nameData,
                $valueData
            );
        }

        return $payload;
    }
}
