<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record;

/**
 * Stdout binary stream
 *
 * FCGI_STDOUT is a stream record for sending arbitrary data from the application to the Web server
 *
 * @author Alexander.Lisachenko
 */
class Stdout extends Record
{

    public function __construct(string $contentData = '')
    {
        $this->type = FCGI::STDOUT;
        $this->setContentData($contentData);
    }

}
