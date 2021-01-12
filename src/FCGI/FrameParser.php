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
 *
 * @author Alexander.Lisachenko
 */
class FrameParser
{
    /**
     * Mapping of constants to the classes
     * @phpstan-var array<int, class-string>
     */
    protected static array $classMapping = [
        FCGI::BEGIN_REQUEST     => FCGI\Record\BeginRequest::class,
        FCGI::ABORT_REQUEST     => FCGI\Record\AbortRequest::class,
        FCGI::END_REQUEST       => FCGI\Record\EndRequest::class,
        FCGI::PARAMS            => FCGI\Record\Params::class,
        FCGI::STDIN             => FCGI\Record\Stdin::class,
        FCGI::STDOUT            => FCGI\Record\Stdout::class,
        FCGI::STDERR            => FCGI\Record\Stderr::class,
        FCGI::DATA              => FCGI\Record\Data::class,
        FCGI::GET_VALUES        => FCGI\Record\GetValues::class,
        FCGI::GET_VALUES_RESULT => FCGI\Record\GetValuesResult::class,
        FCGI::UNKNOWN_TYPE      => FCGI\Record\UnknownType::class,
    ];

    /**
     * Checks if the buffer contains a valid frame to parse
     */
    public static function hasFrame(string $binaryBuffer): bool
    {
        $bufferLength = strlen($binaryBuffer);
        if ($bufferLength < FCGI::HEADER_LEN) {
            return false;
        }

        /** @phpstan-var false|array{version: int, type: int, requestId: int, contentLength: int, paddingLength: int} */
        $fastInfo = unpack(FCGI::HEADER_FORMAT, $binaryBuffer);
        if ($fastInfo === false) {
            throw new \RuntimeException('Can not unpack data from the binary buffer');
        }
        if ($bufferLength < FCGI::HEADER_LEN + $fastInfo['contentLength'] + $fastInfo['paddingLength']) {
            return false;
        }

        return true;
    }

    /**
     * Parses a frame from the binary buffer
     *
     * @return Record One of the corresponding FCGI record
     */
    public static function parseFrame(string &$binaryBuffer): Record
    {
        $bufferLength = strlen($binaryBuffer);
        if ($bufferLength < FCGI::HEADER_LEN) {
            throw new \RuntimeException("Not enough data in the buffer to parse");
        }
        /** @phpstan-var false|array{version: int, type: int, requestId: int, contentLength: int, paddingLength: int} */
        $recordHeader = unpack(FCGI::HEADER_FORMAT, $binaryBuffer);
        if ($recordHeader === false) {
            throw new \RuntimeException('Can not unpack data from the binary buffer');
        }
        $recordType = $recordHeader['type'];
        if (!isset(self::$classMapping[$recordType])) {
            throw new \DomainException("Invalid FCGI record type {$recordType} received");
        }

        /** @var Record $className */
        $className = self::$classMapping[$recordType];
        $record    = $className::unpack($binaryBuffer);

        $offset       = FCGI::HEADER_LEN + $record->getContentLength() + $record->getPaddingLength();
        $binaryBuffer = substr($binaryBuffer, $offset);

        return $record;
    }

}
