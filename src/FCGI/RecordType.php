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
 * Values for the type component of FCGI_Header.
 */
enum RecordType: int
{
    case BeginRequest = 1;
    case AbortRequest = 2;
    case EndRequest = 3;
    case Params = 4;
    case Stdin = 5;
    case Stdout = 6;
    case Stderr = 7;
    case Data = 8;
    case GetValues = 9;
    case GetValuesResult = 10;
    case UnknownType = 11;

    /**
     * Returns the record class implementing this record type.
     *
     * @return class-string<Record>
     */
    public function recordClass(): string
    {
        return match ($this) {
            self::BeginRequest    => Record\BeginRequest::class,
            self::AbortRequest    => Record\AbortRequest::class,
            self::EndRequest      => Record\EndRequest::class,
            self::Params          => Record\Params::class,
            self::Stdin           => Record\Stdin::class,
            self::Stdout          => Record\Stdout::class,
            self::Stderr          => Record\Stderr::class,
            self::Data            => Record\Data::class,
            self::GetValues       => Record\GetValues::class,
            self::GetValuesResult => Record\GetValuesResult::class,
            self::UnknownType     => Record\UnknownType::class,
        };
    }
}
