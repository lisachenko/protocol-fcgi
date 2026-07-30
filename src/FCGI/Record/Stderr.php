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

use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\RecordType;

/**
 * Stderr binary stream
 *
 * FCGI_STDERR is a stream record for sending arbitrary data from the application to the Web server
 */
final class Stderr extends Record
{
    public function __construct(string $contentData)
    {
        $this->type = RecordType::Stderr;
        $this->setContentData($contentData);
    }
}
