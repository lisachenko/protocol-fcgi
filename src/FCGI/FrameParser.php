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

use Lisachenko\Protocol\FCGI;

/**
 * Utility class to simplify parsing of FCGI protocol data.
 */
final class FrameParser
{
    /**
     * Checks if the buffer contains a valid frame to parse
     */
    public static function hasFrame(string $binaryBuffer): bool
    {
        $bufferLength = strlen($binaryBuffer);
        if ($bufferLength < FCGI::HEADER_LEN) {
            return false;
        }

        // Only the two length fields (bytes 4-6) are needed to decide about frame completeness
        /** @var false|array{contentLength: int, paddingLength: int} $lengths */
        $lengths = unpack('ncontentLength/CpaddingLength', $binaryBuffer, 4);
        if ($lengths === false) {
            throw new ProtocolException('Can not unpack the FastCGI record header');
        }

        return $bufferLength >= FCGI::HEADER_LEN + $lengths['contentLength'] + $lengths['paddingLength'];
    }

    /**
     * Parses a frame from the binary buffer, consuming it from the buffer
     *
     * @return Record One of the corresponding FCGI record
     */
    public static function parseFrame(string &$binaryBuffer): Record
    {
        if (strlen($binaryBuffer) < FCGI::HEADER_LEN) {
            throw new ProtocolException('Not enough data in the buffer to parse');
        }

        /** @var false|array{version: int, type: int, requestId: int, contentLength: int, paddingLength: int, reserved: int} $header */
        $header = unpack(FCGI::HEADER_FORMAT, $binaryBuffer);
        if ($header === false) {
            throw new ProtocolException('Can not unpack the FastCGI record header');
        }

        $recordType = RecordType::tryFrom($header['type'])
            ?? throw new ProtocolException("Invalid FastCGI record type {$header['type']} received");

        $record = $recordType->recordClass()::unpack($binaryBuffer);

        $binaryBuffer = substr($binaryBuffer, FCGI::HEADER_LEN + $header['contentLength'] + $header['paddingLength']);

        return $record;
    }
}
