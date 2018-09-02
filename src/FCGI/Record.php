<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI;

use Lisachenko\Protocol\FCGI;

/**
 * FCGI record.
 *
 * @author Alexander.Lisachenko
 */
class Record
{

    /**
     * Identifies the FastCGI protocol version.
     *
     * @var int
     */
    protected $version = FCGI::VERSION_1;

    /**
     * Identifies the FastCGI record type, i.e. the general function that the record performs.
     *
     * @var int
     */
    protected $type = FCGI::UNKNOWN_TYPE;

    /**
     * Identifies the FastCGI request to which the record belongs.
     *
     * @var int
     */
    protected $requestId = FCGI::NULL_REQUEST_ID;

    /**
     * Reserved byte for future proposes
     *
     * @var int
     */
    protected $reserved = 0;

    /**
     * The number of bytes in the contentData component of the record.
     *
     * @var int
     */
    private $contentLength = 0;

    /**
     * The number of bytes in the paddingData component of the record.
     *
     * @var int
     */
    private $paddingLength = 0;

    /**
     * Binary data, between 0 and 65535 bytes of data, interpreted according to the record type.
     *
     * @var string
     */
    private $contentData = '';

    /**
     * Padding data, between 0 and 255 bytes of data, which are ignored.
     *
     * @var string
     */
    private $paddingData = '';

    /**
     * Unpacks the message from the binary data buffer
     *
     * @param string $data Binary buffer with raw data
     *
     * @return static
     */
    final public static function unpack(string $data): self
    {
        $self = new static();
        [
            $self->version,
            $self->type,
            $self->requestId,
            $self->contentLength,
            $self->paddingLength,
            $self->reserved
        ] = array_values(unpack(FCGI::HEADER_FORMAT, $data));

        $payload = substr($data, FCGI::HEADER_LEN);
        self::unpackPayload($self, $payload);
        if (get_called_class() !== __CLASS__ && $self->contentLength > 0) {
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
     *
     * @param string $data
     *
     * @return static
     */
    public function setContentData(string $data): self
    {
        $this->contentData = $data;
        $this->contentLength = strlen($this->contentData);
        $extraLength = $this->contentLength % 8;
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
     *
     * @param int $requestId
     *
     * @return static
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
     *
     * @param static $self Instance of current frame
     * @param string $data Binary data
     */
    protected static function unpackPayload($self, string $data): void
    {
        [
            $self->contentData,
            $self->paddingData
        ] = array_values(
            unpack("a{$self->contentLength}contentData/a{$self->paddingLength}paddingData", $data)
        );
    }

    /**
     * Implementation of packing the payload
     */
    protected function packPayload(): string
    {
        return pack("a{$this->contentLength}", $this->contentData);
    }

}
