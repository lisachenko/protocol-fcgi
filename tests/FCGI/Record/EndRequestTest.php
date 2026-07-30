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

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI\ProtocolStatus;
use Lisachenko\Protocol\FCGI\RecordType;

class EndRequestTest extends TestCase
{
    protected static string $rawMessage = '01030000000800000000006400000000';

    public function testPacking(): void
    {
        $request = new EndRequest(ProtocolStatus::RequestComplete, 100);
        $this->assertSame(RecordType::EndRequest, $request->getType());
        $this->assertSame(ProtocolStatus::RequestComplete, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = EndRequest::unpack($binaryData);

        $this->assertSame(RecordType::EndRequest, $request->getType());
        $this->assertSame(ProtocolStatus::RequestComplete, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());
    }
}
