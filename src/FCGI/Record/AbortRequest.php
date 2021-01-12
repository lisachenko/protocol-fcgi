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
 * The Web server sends a FCGI_ABORT_REQUEST record to abort a request
 *
 * @author Alexander.Lisachenko
 */
class AbortRequest extends Record
{
    public function __construct(int $requestId = 0)
    {
        $this->type = FCGI::ABORT_REQUEST;
        $this->setRequestId($requestId);
    }
}
