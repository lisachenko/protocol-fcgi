<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record;

/**
 * Data binary stream
 *
 * FCGI_DATA is a second stream record type used to send additional data to the application.
 */
class Data extends Record
{
    public function __construct(string $contentData)
    {
        $this->type = FCGI::DATA;
        $this->setContentData($contentData);
    }
}
