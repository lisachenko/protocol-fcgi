<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class UnknownTypeTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '010b0000000800002a57544621000000';

    public function testPacking()
    {
        $request = new UnknownType(42, 'WTF!');
        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = UnknownType::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());
    }
}