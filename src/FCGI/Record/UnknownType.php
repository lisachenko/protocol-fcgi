<?php
/**
 * @author Alexander.Lisachenko
 * @date 14.07.2014
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

class UnknownType extends Record
{
    /**
     * Type of the unrecognized management record.
     *
     * @var integer
     */
    protected $type1;

    /**
     * Reserved data, 7 bytes maximum
     *
     * @var string
     */
    protected $reserved1;

    public function __construct($type = 0, $reserved = '')
    {
        $this->type        = FCGI::UNKNOWN_TYPE;
        $this->type1       = $type;
        $this->reserved1   = $reserved;
        $this->setContentData($this->packPayload());
    }

    /**
     * Method to unpack the payload for the record
     *
     * @param Record $self Instance of current frame
     * @param string $data Binary data
     *
     * @return Record
     */
    public static function unpackPayload(Record $self, $data)
    {
        list(
            $self->type1,
            $self->reserved1
        ) = array_values(unpack("Ctype/a7reserved", $data));

        return $self;
    }

    /**
     * Implementation of packing the payload
     *
     * @return string
     */
    protected function packPayload()
    {
        return pack(
            "Ca7",
            $this->type1,
            $this->reserved1
        );
    }
}
