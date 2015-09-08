<?php
/**
 * @author Alexander.Lisachenko
 * @date 08.09.2015
 */

namespace Protocol\FCGI;

use Protocol\FCGI;

/**
 * Utility class to simplify parsing of FCGI protocol data
 */
class FrameParser
{
    /**
     * Mapping of constants to the classes
     *
     * @var array
     */
    protected static $classMapping = [
        FCGI::BEGIN_REQUEST      => FCGI\Record\BeginRequest::class,
        FCGI::ABORT_REQUEST      => FCGI\Record\AbortRequest::class,
        FCGI::END_REQUEST        => FCGI\Record\EndRequest::class,
        FCGI::PARAMS             => FCGI\Record\Params::class,
        FCGI::STDIN              => FCGI\Record\Stdin::class,
        FCGI::STDOUT             => FCGI\Record\Stdout::class,
        FCGI::STDERR             => FCGI\Record\Stderr::class,
        FCGI::DATA               => FCGI\Record\Data::class,
        FCGI::GET_VALUES         => FCGI\Record\GetValues::class,
        FCGI::GET_VALUES_RESULT  => FCGI\Record\GetValuesResult::class,
        FCGI::UNKNOWN_TYPE       => FCGI\Record\UnknownType::class,
    ];

    /**
     * Checks if the buffer contains a valid frame to parse
     *
     * @param string $buffer Binary buffer
     *
     * @return bool
     */
    public static function hasFrame($buffer)
    {
        $bufferLength = strlen($buffer);
        if ($bufferLength < FCGI::HEADER_LEN) {
            return false;
        }

        $fastInfo = unpack(FCGI::HEADER_FORMAT, $buffer);
        if ($bufferLength < FCGI::HEADER_LEN + $fastInfo['contentLength'] + $fastInfo['paddingLength']) {
            return false;
        }

        return true;
    }

    /**
     * Parses a frame from the binary buffer
     *
     * @param string $buffer Binary buffer
     *
     * @return Record One of the corresponding FCGI record
     */
    public static function parseFrame(&$buffer)
    {
        $bufferLength = strlen($buffer);
        if ($bufferLength < FCGI::HEADER_LEN) {
            throw new \RuntimeException("Not enough data in the buffer to parse");
        }
        $recordHeader = unpack(FCGI::HEADER_FORMAT, $buffer);
        $recordType   = $recordHeader['type'];
        if (!isset(self::$classMapping[$recordType])) {
            throw new \DomainException("Invalid FCGI record type {$recordType} received");
        }

        /** @var Record $className */
        $className = self::$classMapping[$recordType];
        $record    = $className::unpack($buffer);


        $offset = FCGI::HEADER_LEN + $record->getContentLength() + $record->getPaddingLength();
        $buffer = substr($buffer, $offset);

        return $record;
    }
}
