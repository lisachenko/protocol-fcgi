<?php
/**
 * @author Alexander.Lisachenko
 * @date 14.07.2014
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

/**
 * The application sends a FCGI_END_REQUEST record to terminate a request, either because the application
 * has processed the request or because the application has rejected the request.
 */
class EndRequest extends Record
{
    /**
     * The appStatus component is an application-level status code. Each role documents its usage of appStatus.
     *
     * @var integer
     */
    protected $appStatus = 0;

    /**
     * The protocolStatus component is a protocol-level status code.
     *
     * The possible protocolStatus values are:
     *   FCGI_REQUEST_COMPLETE: normal end of request.
     *   FCGI_CANT_MPX_CONN: rejecting a new request.
     *      This happens when a Web server sends concurrent requests over one connection to an application that is
     *      designed to process one request at a time per connection.
     *   FCGI_OVERLOADED: rejecting a new request.
     *      This happens when the application runs out of some resource, e.g. database connections.
     *   FCGI_UNKNOWN_ROLE: rejecting a new request.
     *      This happens when the Web server has specified a role that is unknown to the application.
     *
     *
     * @var integer
     */
    protected $protocolStatus = FCGI::REQUEST_COMPLETE;

    /**
     * Reserved data, 3 bytes maximum
     *
     * @var string
     */
    protected $reserved1;

    public function __construct($protocolStatus = FCGI::REQUEST_COMPLETE, $appStatus = 0, $reserved = '')
    {
        $this->type           = FCGI::END_REQUEST;
        $this->protocolStatus = $protocolStatus;
        $this->appStatus      = $appStatus;
        $this->reserved1      = $reserved;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns app status
     *
     * The appStatus component is an application-level status code. Each role documents its usage of appStatus.
     *
     * @return int
     */
    public function getAppStatus()
    {
        return $this->appStatus;
    }

    /**
     * Returns the protocol status
     *
     * The possible protocolStatus values are:
     *   FCGI_REQUEST_COMPLETE: normal end of request.
     *   FCGI_CANT_MPX_CONN: rejecting a new request.
     *      This happens when a Web server sends concurrent requests over one connection to an application that is
     *      designed to process one request at a time per connection.
     *   FCGI_OVERLOADED: rejecting a new request.
     *      This happens when the application runs out of some resource, e.g. database connections.
     *   FCGI_UNKNOWN_ROLE: rejecting a new request.
     *      This happens when the Web server has specified a role that is unknown to the application.
     *
     * @return int
     */
    public function getProtocolStatus()
    {
        return $this->protocolStatus;
    }

    /**
     * Method to unpack the payload for the record
     *
     * @param Record|self $self Instance of current frame
     * @param string $data Binary data
     *
     * @return Record
     */
    protected static function unpackPayload(Record $self, $data)
    {
        list(
            $self->appStatus,
            $self->protocolStatus,
            $self->reserved1
        ) = array_values(unpack("NappStatus/CprotocolStatus/a3reserved", $data));

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
            "NCa3",
            $this->appStatus,
            $this->protocolStatus,
            $this->reserved1
        );
    }
}
