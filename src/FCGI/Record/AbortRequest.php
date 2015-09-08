<?php
/**
 * @author Alexander.Lisachenko
 * @date 08.09.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

/**
 * The Web server sends a FCGI_ABORT_REQUEST record to abort a request
 */
class AbortRequest extends Record
{

    public function __construct($requestId = 0)
    {
        $this->type = FCGI::ABORT_REQUEST;
        $this->setRequestId($requestId);
    }
}
