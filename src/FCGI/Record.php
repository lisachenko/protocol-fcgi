<?php
/**
 * @author Alexander.Lisachenko
 * @date 14.07.2014
 */

namespace Protocol\FCGI;

use Protocol\FCGI;

/**
 * FCGI Record class
 */
class Record
{
    /**
     * Identifies the FastCGI protocol version.
     *
     * @var integer
     */
    protected $version = FCGI::VERSION_1;

    /**
     * Identifies the FastCGI record type, i.e. the general function that the record performs.
     *
     * @var integer
     */
    protected $type = FCGI::UNKNOWN_TYPE;

    /**
     * Identifies the FastCGI request to which the record belongs.
     *
     * @var integer
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
     * @var integer
     */
    private $contentLength = 0;

    /**
     * The number of bytes in the paddingData component of the record.
     *
     * @var integer
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
    final public static function unpack($data)
    {
        $self = new static();
        list(
            $self->version,
            $self->type,
            $self->requestId,
            $self->contentLength,
            $self->paddingLength,
            $self->reserved
        ) = array_values(unpack(FCGI::HEADER_FORMAT, $data));

        $payload = substr($data, FCGI::HEADER_LEN);
        self::unpackPayload($self, $payload);
        if (get_called_class() !== __CLASS__ && $self->contentLength > 0) {
            static::unpackPayload($self, $payload);
        }

        return $self;
    }

    /**
     * Returns the binary message representation of record
     *
     * @return string
     */
    final public function __toString()
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
     * @param $data
     */
    public function setContentData($data)
    {
        $this->contentData   = $data;
        $this->contentLength = strlen($this->contentData);
        $extraLength         = $this->contentLength % 8;
        $this->paddingLength = $extraLength ? (8 - $extraLength) : 0;
    }

    /**
     * Returns the context data from the record
     *
     * @return string
     */
    public function getContentData()
    {
        return $this->contentData;
    }

    /**
     * Returns the version of record
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param int $requestId
     *
     * @return Record
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Returns the size of content length
     *
     * @return int
     */
    final public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * Returns the size of padding length
     *
     * @return int
     */
    final public function getPaddingLength()
    {
        return $this->paddingLength;
    }

    /**
     * Method to unpack the payload for the record.
     *
     * NB: Default implementation will be always called
     *
     * @param Record $self Instance of current frame
     * @param string $data Binary data
     */
    protected static function unpackPayload(Record $self, $data)
    {
        list(
            $self->contentData,
            $self->paddingData
        ) = array_values(
            unpack("a{$self->contentLength}contentData/a{$self->paddingLength}paddingData", $data)
        );
    }

    /**
     * Implementation of packing the payload
     *
     * @return string
     */
    protected function packPayload()
    {
        return pack("a{$this->contentLength}", $this->contentData);
    }
}
