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
 * Stdout binary stream
 *
 * FCGI_STDOUT is a stream record for sending arbitrary data from the application to the Web server
 */
final class Stdout extends Record
{
    public function __construct(string $contentData)
    {
        $this->type = RecordType::Stdout;
        $this->setContentData($contentData);
    }
}
