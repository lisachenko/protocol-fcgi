<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class AbortRequestTest extends TestCase
{
    protected static $rawMessage = '0102000100000000';

    public function testPacking(): void
    {
        $request = new AbortRequest(1);
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = AbortRequest::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());
    }
}
