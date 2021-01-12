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
use ReflectionClass;

/**
 * FCGI record.
 */
class Record
{
    /**
     * Identifies the FastCGI protocol version.
     */
    protected int $version = FCGI::VERSION_1;

    /**
     * Identifies the FastCGI record type, i.e. the general function that the record performs.
     */
    protected int $type = FCGI::UNKNOWN_TYPE;

    /**
     * Identifies the FastCGI request to which the record belongs.
     */
    protected int $requestId = FCGI::NULL_REQUEST_ID;

    /**
     * Reserved byte for future proposes
     */
    protected int $reserved = 0;

    /**
     * The number of bytes in the contentData component of the record.
     */
    private int $contentLength = 0;

    /**
     * The number of bytes in the paddingData component of the record.
     */
    private int $paddingLength = 0;

    /**
     * Binary data, between 0 and 65535 bytes of data, interpreted according to the record type.
     */
    private string $contentData = '';

    /**
     * Padding data, between 0 and 255 bytes of data, which are ignored.
     */
    private string $paddingData = '';

    /**
     * Unpacks the message from the binary data buffer
     *
     * @return static
     */
    final public static function unpack(string $binaryData): self
    {
        /** @var static $self */
        $self   = (new ReflectionClass(static::class))->newInstanceWithoutConstructor();

        /** @phpstan-var false|array{version: int, type: int, requestId: int, contentLength: int, paddingLength: int} */
        $packet = unpack(FCGI::HEADER_FORMAT, $binaryData);
        if ($packet === false) {
            throw new \RuntimeException('Can not unpack data from the binary buffer');
        }
        [
            $self->version,
            $self->type,
            $self->requestId,
            $self->contentLength,
            $self->paddingLength,
            $self->reserved
        ] = array_values($packet);

        $payload = substr($binaryData, FCGI::HEADER_LEN);
        self::unpackPayload($self, $payload);
        if (static::class !== self::class && $self->contentLength > 0) {
            static::unpackPayload($self, $payload);
        }

        return $self;
    }

    /**
     * Returns the binary message representation of record
     */
    final public function __toString(): string
    {
        $headerPacket = pack(
            "CCnnCC",
            $this->version,
            $this->type,
            $this->requestId,
            $this->contentLength,
            $this->paddingLength,
            $this->reserved
        );

        $payloadPacket = $this->packPayload();
        $paddingPacket = pack("a{$this->paddingLength}", $this->paddingData);

        return $headerPacket . $payloadPacket . $paddingPacket;
    }

    /**
     * Sets the content data and adjusts the length fields
     */
    public function setContentData(string $data): self
    {
        $this->contentData   = $data;
        $this->contentLength = strlen($this->contentData);
        $extraLength         = $this->contentLength % 8;
        $this->paddingLength = $extraLength ? (8 - $extraLength) : 0;

        return $this;
    }

    /**
     * Returns the context data from the record
     */
    public function getContentData(): string
    {
        return $this->contentData;
    }

    /**
     * Returns the version of record
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Returns record type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Returns request ID
     */
    public function getRequestId(): int
    {
        return $this->requestId;
    }

    /**
     * Sets request ID
     *
     * There should be only one unique ID for all active requests,
     * use random number or preferably resetting auto-increment.
     */
    public function setRequestId(int $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Returns the size of content length
     */
    final public function getContentLength(): int
    {
        return $this->contentLength;
    }

    /**
     * Returns the size of padding length
     */
    final public function getPaddingLength(): int
    {
        return $this->paddingLength;
    }

    /**
     * Method to unpack the payload for the record.
     *
     * NB: Default implementation will be always called
     * @param static $self
     */
    protected static function unpackPayload(Record $self, string $binaryData): void
    {
        /** @phpstan-var false|array{contentData: string, paddingData: string} */
        $payload = unpack("a{$self->contentLength}contentData/a{$self->paddingLength}paddingData", $binaryData);
        if ($payload === false) {
            throw new \RuntimeException('Can not unpack data from the binary buffer');
        }
        [
            $self->contentData,
            $self->paddingData
        ] = array_values($payload);
    }

    /**
     * Implementation of packing the payload
     */
    protected function packPayload(): string
    {
        return pack("a{$this->contentLength}", $this->contentData);
    }
}
