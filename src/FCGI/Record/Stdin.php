<?php
/**
 * @author Alexander.Lisachenko
 * @date 08.09.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

/**
 * Stdin binary stream
 *
 * FCGI_STDIN is a stream record type used in sending arbitrary data from the Web server to the application
 */
class Stdin extends Record
{
    public function __construct($contentData = '')
    {
        $this->type = FCGI::STDIN;
        $this->setContentData($contentData);
    }
}
