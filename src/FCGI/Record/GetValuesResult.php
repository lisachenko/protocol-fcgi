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

use Lisachenko\Protocol\FCGI\RecordType;

/**
 * GetValues API
 *
 * The application responds to a FCGI_GET_VALUES management record by sending a record
 * {FCGI_GET_VALUES_RESULT, 0, ...} with the values supplied.
 * If the application doesn't understand a variable name that was included in the query, it omits that name from
 * the response.
 */
final class GetValuesResult extends Params
{
    /**
     * Constructs a param request
     *
     * @param array<string, string> $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
        $this->type = RecordType::GetValuesResult;
    }
}
