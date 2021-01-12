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
 * FCGI constants.
 */
class FCGI
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
     * Values for type component of FCGI_Header
     */
    public const
        BEGIN_REQUEST = 1,
        ABORT_REQUEST = 2,
        END_REQUEST = 3,
        PARAMS = 4,
        STDIN = 5,
        STDOUT = 6,
        STDERR = 7,
        DATA = 8,
        GET_VALUES = 9,
        GET_VALUES_RESULT = 10,
        UNKNOWN_TYPE = 11;

    /**
     * Value for requestId component of FCGI_Header
     */
    public const NULL_REQUEST_ID = 0;

    /**
     * Mask for flags component of FCGI_BeginRequestBody
     */
    public const KEEP_CONN = 1;

    /**
     * Values for role component of FCGI_BeginRequestBody
     */
    public const
        RESPONDER = 1,
        AUTHORIZER = 2,
        FILTER = 3;

    /**
     * Values for protocolStatus component of FCGI_EndRequestBody
     */
    public const
        REQUEST_COMPLETE = 0,
        CANT_MPX_CONN = 1,
        OVERLOADED = 2,
        UNKNOWN_ROLE = 3;

}
