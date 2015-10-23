<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class StdoutTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01060000000404007465737400000000';

    public function testPacking()
    {
        $request = new Stdout('test');
        $this->assertEquals($request->getContentData(), 'test');
        $this->assertEquals($request->getType(), FCGI::STDOUT);
        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = Stdout::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals($request->getType(), FCGI::STDOUT);
        $this->assertEquals($request->getContentData(), 'test');
    }
}