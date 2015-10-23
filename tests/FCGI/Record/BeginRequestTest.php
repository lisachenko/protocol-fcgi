<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class BeginRequestTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01010000000800000001010000000000';

    public function testPacking()
    {
        $request = new BeginRequest(FCGI::RESPONDER, FCGI::KEEP_CONN);
        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = BeginRequest::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());
    }
}