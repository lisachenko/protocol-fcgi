<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class GetValuesTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '01090000001107000f00464347495f4d5058535f434f4e4e5300000000000000';

    public function testPacking()
    {
        $request = new GetValues(array('FCGI_MPXS_CONNS'));
        $this->assertEquals(FCGI::GET_VALUES, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => ''), $request->getValues());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = GetValues::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::GET_VALUES, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => ''), $request->getValues());
    }
}