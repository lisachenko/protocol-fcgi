<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol;

/**
 * FCGI protocol constants.
 *
 * Record types, roles and protocol statuses are represented by the native enums
 * {@see FCGI\RecordType}, {@see FCGI\Role} and {@see FCGI\ProtocolStatus}.
 */
final class FCGI
{
    /**
     * Number of bytes in a FCGI_Header.  Future versions of the protocol
     * will not reduce this number.
     */
    public const HEADER_LEN = 8;

    /**
     * Format of FCGI_HEADER for unpacking in PHP
     */
    public const HEADER_FORMAT = "Cversion/Ctype/nrequestId/ncontentLength/CpaddingLength/Creserved";

    /**
     * Value for version component of FCGI_Header
     */
    public const VERSION_1 = 1;

    /**
     * Value for requestId component of FCGI_Header
     */
    public const NULL_REQUEST_ID = 0;

    /**
     * Mask for flags component of FCGI_BeginRequestBody
     */
    public const KEEP_CONN = 1;

    /**
     * This is a static holder of protocol constants, it can not be instantiated.
     */
    private function __construct()
    {
    }
}
