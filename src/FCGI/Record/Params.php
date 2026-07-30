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

use Lisachenko\Protocol\FCGI\ProtocolException;
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\RecordType;

/**
 * Params request record
 */
class Params extends Record
{
    /**
     * List of params
     *
     * NB: this property intentionally keeps its default value instead of being promoted
     * to the constructor: an empty FCGI_PARAMS record (the end-of-stream marker) is
     * hydrated from the wire without running the constructor or unpackPayload().
     *
     * @var array<string, string>
     */
    protected array $values = [];

    /**
     * Constructs a param request
     *
     * @param array<string, string> $values
     */
    public function __construct(array $values)
    {
        $this->type   = RecordType::Params;
        $this->values = $values;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns an associative list of parameters
     *
     * @return array<string, string>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    protected function unpackPayload(string $binaryData): void
    {
        $currentOffset = 0;
        do {
            /** @var false|array{nameLengthHigh: int} $payload */
            $payload = unpack('CnameLengthHigh', $binaryData);
            if ($payload === false) {
                throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
            }
            $isLongName  = ($payload['nameLengthHigh'] >> 7 == 1);
            $valueOffset = $isLongName ? 4 : 1;

            /** @var false|array{valueLengthHigh: int} $payload */
            $payload = unpack('CvalueLengthHigh', substr($binaryData, $valueOffset));
            if ($payload === false) {
                throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
            }
            $isLongValue = ($payload['valueLengthHigh'] >> 7 == 1);
            $dataOffset  = $valueOffset + ($isLongValue ? 4 : 1);

            $format = ($isLongName ? 'NnameLength' : 'CnameLength')
                . '/' . ($isLongValue ? 'NvalueLength' : 'CvalueLength');

            /** @var false|array{nameLength: int, valueLength: int} $payload */
            $payload = unpack($format, $binaryData);
            if ($payload === false) {
                throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
            }

            // Clear top bit for long record
            $nameLength  = $payload['nameLength'] & ($isLongName ? 0x7fffffff : 0x7f);
            $valueLength = $payload['valueLength'] & ($isLongValue ? 0x7fffffff : 0x7f);

            /** @var false|array{nameData: string, valueData: string} $payload */
            $payload = unpack(
                "a{$nameLength}nameData/a{$valueLength}valueData",
                substr($binaryData, $dataOffset)
            );
            if ($payload === false) {
                throw new ProtocolException('Can not unpack the FCGI_NameValuePair');
            }

            $this->values[$payload['nameData']] = $payload['valueData'];

            $keyValueLength = $dataOffset + $nameLength + $valueLength;
            $binaryData     = substr($binaryData, $keyValueLength);
            $currentOffset += $keyValueLength;
        } while ($currentOffset < $this->getContentLength());
    }

    protected function packPayload(): string
    {
        $payload = '';
        foreach ($this->values as $nameData => $valueData) {
            $nameLength  = strlen($nameData);
            $valueLength = strlen($valueData);
            $isLongName  = $nameLength > 127;
            $isLongValue = $valueLength > 127;

            $format = ($isLongName ? 'N' : 'C')
                . ($isLongValue ? 'N' : 'C')
                . "a{$nameLength}a{$valueLength}";

            $payload .= pack(
                $format,
                $isLongName ? ($nameLength | 0x80000000) : $nameLength,
                $isLongValue ? ($valueLength | 0x80000000) : $valueLength,
                $nameData,
                $valueData
            );
        }

        return $payload;
    }
}
