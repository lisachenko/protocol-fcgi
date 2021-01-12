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

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

class RecordTest extends TestCase
{
    // from the wireshark captured traffic
    static string $rawRequest = '01010001000800000001010000000000';

    public function testUnpackingPacket(): void
    {
        /** @var string $packet */
        $packet = hex2bin(self::$rawRequest);
        $record = Record::unpack($packet);

        // Verify all general fields
        $this->assertEquals(FCGI::VERSION_1, $record->getVersion());
        $this->assertEquals(FCGI::BEGIN_REQUEST, $record->getType());
        $this->assertEquals(1, $record->getRequestId());
        $this->assertEquals(8, $record->getContentLength());
        $this->assertEquals(0, $record->getPaddingLength());

        // Check payload data
        $this->assertEquals(hex2bin('0001010000000000'), $record->getContentData());
    }

    public function testPackingPacket(): void
    {
        $record = new Record();
        $record->setRequestId(5);
        $record->setContentData('12345');
        $packet = (string) $record;

        $this->assertEquals($packet, hex2bin('010b0005000503003132333435000000'));
        $result = Record::unpack($packet);
        $this->assertEquals(FCGI::UNKNOWN_TYPE, $result->getType());
        $this->assertEquals(5, $result->getRequestId());
        $this->assertEquals('12345', $result->getContentData());
    }

    /**
     * Padding size should resize the packet size to the 8 bytes boundary for optimal performance
     */
    public function testAutomaticCalculationOfPaddingLength(): void
    {
        $record = new Record();
        $record->setContentData('12345');
        $this->assertEquals(3, $record->getPaddingLength());

        $record->setContentData('12345678');
        $this->assertEquals(0, $record->getPaddingLength());
    }
}
