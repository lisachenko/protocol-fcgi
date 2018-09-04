<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class BeginRequestTest extends TestCase
{
    protected static $rawMessage = '01010000000800000001010000000000';

    public function testPacking(): void
    {
        $request = new BeginRequest(FCGI::RESPONDER, FCGI::KEEP_CONN);
        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = BeginRequest::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());
    }
}
