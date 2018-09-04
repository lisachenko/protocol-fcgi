<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record;

/**
 * Data binary stream
 *
 * FCGI_DATA is a second stream record type used to send additional data to the application.
 *
 * @author Alexander.Lisachenko
 */
class Data extends Record
{

    public function __construct(string $contentData = '')
    {
        $this->type = FCGI::DATA;
        $this->setContentData($contentData);
    }

}
