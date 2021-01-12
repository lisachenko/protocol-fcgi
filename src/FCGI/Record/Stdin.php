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
 * Stdin binary stream
 *
 * FCGI_STDIN is a stream record type used in sending arbitrary data from the Web server to the application
 *
 * @author Alexander.Lisachenko
 */
class Stdin extends Record
{
    public function __construct(string $contentData = '')
    {
        $this->type = FCGI::STDIN;
        $this->setContentData($contentData);
    }
}
