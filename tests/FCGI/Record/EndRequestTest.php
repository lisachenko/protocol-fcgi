<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class EndRequestTest extends TestCase
{
    protected static $rawMessage = '01030000000800000000006400000000';

    public function testPacking(): void
    {
        $request = new EndRequest(FCGI::REQUEST_COMPLETE, 100);
        $this->assertEquals(FCGI::END_REQUEST, $request->getType());
        $this->assertEquals(FCGI::REQUEST_COMPLETE, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = EndRequest::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::END_REQUEST, $request->getType());
        $this->assertEquals(FCGI::REQUEST_COMPLETE, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());
    }
}
