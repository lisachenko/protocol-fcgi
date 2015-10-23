<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;

class ParamsTest extends \PHPUnit_Framework_TestCase
{
    protected static $rawMessage ='
        01040000005b05000f0e5343524950545f46494c454e414d452f686f6d652f746573742e7068701
        107474154455741595f494e544552464143454347492f312e310f115345525645525f534f465457
        4152455048502f50726f746f636f6c2d464347490000000000';

    protected static $params = array(
        'SCRIPT_FILENAME'   => '/home/test.php',
        'GATEWAY_INTERFACE' => "CGI/1.1",
        'SERVER_SOFTWARE'   => "PHP/Protocol-FCGI",
    );

    public function testPacking()
    {
        $request = new Params(self::$params);
        $this->assertEquals($request->getType(), FCGI::PARAMS);
        $this->assertEquals(self::$params, $request->getValues());

        $this->assertSame(preg_replace('/\s+/', '', self::$rawMessage), bin2hex($request));
    }

    public function testUnpacking()
    {
        $request = Params::unpack(hex2bin(preg_replace('/\s+/', '', self::$rawMessage)));

        $this->assertEquals($request->getType(), FCGI::PARAMS);
        $this->assertEquals($request->getValues(), self::$params);
    }
}