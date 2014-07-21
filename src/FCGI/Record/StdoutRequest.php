<?php
/**
 * @author Alexander.Lisachenko
 * @date 14.07.2014
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

class StdoutRequest extends Record
{
    public function __construct($contentData = '')
    {
        $this->type = FCGI::STDOUT;
        $this->setContentData($contentData);
    }
}
