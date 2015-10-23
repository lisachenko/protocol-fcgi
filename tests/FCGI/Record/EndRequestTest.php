<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class EndRequestTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01030000000800000000006400000000';

    public function testPacking()
    {
        $request = new EndRequest(FCGI::REQUEST_COMPLETE, 100);
        $this->assertEquals(FCGI::END_REQUEST, $request->getType());
        $this->assertEquals(FCGI::REQUEST_COMPLETE, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = EndRequest::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::END_REQUEST, $request->getType());
        $this->assertEquals(FCGI::REQUEST_COMPLETE, $request->getProtocolStatus());
        $this->assertEquals(100, $request->getAppStatus());
    }
}