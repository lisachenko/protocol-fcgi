<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class AbortRequestTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '0102000100000000';

    public function testPacking()
    {
        $request = new AbortRequest(1);
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = AbortRequest::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());
    }
}