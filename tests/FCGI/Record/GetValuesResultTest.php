<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class GetValuesResultTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage = '010a0000001206000f01464347495f4d5058535f434f4e4e5331000000000000';

    public function testPacking()
    {
        $request = new GetValuesResult(array('FCGI_MPXS_CONNS' => 1));
        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => 1), $request->getValues());

        $this->assertSame(self::$rawMessage, bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = GetValuesResult::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => 1), $request->getValues());
    }
}