<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class StdinTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01050000000404007465737400000000';

    public function testPacking()
    {
        $request = new Stdin('test');
        $this->assertEquals($request->getContentData(), 'test');
        $this->assertEquals($request->getType(), FCGI::STDIN);
        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = Stdin::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals($request->getType(), FCGI::STDIN);
        $this->assertEquals($request->getContentData(), 'test');
    }
}