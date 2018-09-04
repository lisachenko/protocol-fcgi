<?php declare(strict_types=1);

namespace Lisachenko\Protocol;

/**
 * FCGI constants.
 *
 * @author Alexander.Lisachenko
 */
class FCGI
{

    /**
     * Number of bytes in a FCGI_Header.  Future versions of the protocol
     * will not reduce this number.
     */
    const HEADER_LEN = 8;

    /**
     * Format of FCGI_HEADER for unpacking in PHP
     */
    const HEADER_FORMAT = "Cversion/Ctype/nrequestId/ncontentLength/CpaddingLength/Creserved";

    /**
     * Value for version component of FCGI_Header
     */
    const VERSION_1 = 1;

    /**
     * Values for type component of FCGI_Header
     */
    const
        BEGIN_REQUEST     = 1,
        ABORT_REQUEST     = 2,
        END_REQUEST       = 3,
        PARAMS            = 4,
        STDIN             = 5,
        STDOUT            = 6,
        STDERR            = 7,
        DATA              = 8,
        GET_VALUES        = 9,
        GET_VALUES_RESULT = 10,
        UNKNOWN_TYPE      = 11;

    /**
     * Value for requestId component of FCGI_Header
     */
    const NULL_REQUEST_ID = 0;

    /**
     * Mask for flags component of FCGI_BeginRequestBody
     */
    const KEEP_CONN = 1;

    /**
     * Values for role component of FCGI_BeginRequestBody
     */
    const
        RESPONDER  = 1,
        AUTHORIZER = 2,
        FILTER     = 3;

    /**
     * Values for protocolStatus component of FCGI_EndRequestBody
     */
    const
        REQUEST_COMPLETE = 0,
        CANT_MPX_CONN    = 1,
        OVERLOADED       = 2,
        UNKNOWN_ROLE     = 3;

}
