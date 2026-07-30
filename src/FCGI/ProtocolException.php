<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI;

/**
 * Thrown when binary data violates the FastCGI protocol: truncated or malformed
 * buffers, unknown record types, or invalid enumeration values on the wire.
 */
class ProtocolException extends \RuntimeException
{
}
