<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class DataTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01080000000404007465737400000000';

    public function testPacking()
    {
        $request = new Data('test');
        $this->assertEquals('test', $request->getContentData());
        $this->assertEquals(FCGI::DATA, $request->getType());
        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = Data::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals(FCGI::DATA, $request->getType());
        $this->assertEquals('test', $request->getContentData());
    }
}