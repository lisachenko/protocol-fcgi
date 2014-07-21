<?php
/**
 * @author Alexander.Lisachenko
 * @date 14.07.2014
 */

namespace Protocol\FCGI;

use Protocol\FCGI;

/**
 * FCGI Record class
 *
 * @property-read integer $contentLength Length of the content
 * @property-read integer $paddingLength Length of the padding field
 * @property-read string $paddingData Padding-data
 * @property string $contentData Payload for the record
 */
class Record
{
    /**
     * Identifies the FastCGI protocol version.
     *
     * @var integer
     */
    public $version = FCGI::VERSION_1;

    /**
     * Identifies the FastCGI record type, i.e. the general function that the record performs.
     *
     * @var integer
     */
    public $type = FCGI::UNKNOWN_TYPE;

    /**
     * Identifies the FastCGI request to which the record belongs.
     *
     * @var integer
     */
    public $requestId = FCGI::NULL_REQUEST_ID;

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
     * Reserved byte for future proposes
     *
     * @var int
     */
    private $reserved = 0;

    public static function unpack($data)
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

    public function __toString()
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
     * Magic properties accessor
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            return null;
        }

        return $this->$name;
    }

    /**
     * Magic property writer
     *
     * @param string $name Property name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $setterName = 'set' . ucfirst($name);
        if (method_exists($this, $setterName)) {
            $this->$setterName($value);
        }
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
