<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace PHPSTORM_META;

expectedArguments(
    \Lisachenko\Protocol\FCGI\Record\BeginRequest::__construct(),
    0,
    \Lisachenko\Protocol\FCGI::RESPONDER,
    \Lisachenko\Protocol\FCGI::AUTHORIZER,
    \Lisachenko\Protocol\FCGI::FILTER,
);

expectedArguments(
    \Lisachenko\Protocol\FCGI\Record\BeginRequest::__construct(),
    1,
    \Lisachenko\Protocol\FCGI::KEEP_CONN,
);

expectedArguments(
    \Lisachenko\Protocol\FCGI\Record\EndRequest::__construct(),
    0,
    \Lisachenko\Protocol\FCGI::KEEP_CONN,
    \Lisachenko\Protocol\FCGI::REQUEST_COMPLETE,
    \Lisachenko\Protocol\FCGI::CANT_MPX_CONN,
    \Lisachenko\Protocol\FCGI::OVERLOADED,
    \Lisachenko\Protocol\FCGI::UNKNOWN_ROLE
);

expectedReturnValues(
    \Lisachenko\Protocol\FCGI\Record::getVersion(),
    \Lisachenko\Protocol\FCGI::VERSION_1
);

expectedReturnValues(
    \Lisachenko\Protocol\FCGI\Record::getType(),
    \Lisachenko\Protocol\FCGI::BEGIN_REQUEST,
    \Lisachenko\Protocol\FCGI::ABORT_REQUEST,
    \Lisachenko\Protocol\FCGI::END_REQUEST,
    \Lisachenko\Protocol\FCGI::PARAMS,
    \Lisachenko\Protocol\FCGI::STDIN,
    \Lisachenko\Protocol\FCGI::STDOUT,
    \Lisachenko\Protocol\FCGI::STDERR,
    \Lisachenko\Protocol\FCGI::DATA,
    \Lisachenko\Protocol\FCGI::GET_VALUES,
    \Lisachenko\Protocol\FCGI::GET_VALUES_RESULT,
    \Lisachenko\Protocol\FCGI::UNKNOWN_TYPE,
);
