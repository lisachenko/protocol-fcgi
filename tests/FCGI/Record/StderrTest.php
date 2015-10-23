<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class StderrTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01070000000404007465737400000000';

    public function testPacking()
    {
        $request = new Stderr('test');
        $this->assertEquals($request->getContentData(), 'test');
        $this->assertEquals($request->getType(), FCGI::STDERR);
        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = Stderr::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals($request->getType(), FCGI::STDERR);
        $this->assertEquals($request->getContentData(), 'test');
    }
}