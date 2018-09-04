<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class GetValuesResultTest extends TestCase
{
    protected static $rawMessage = '010a0000001206000f01464347495f4d5058535f434f4e4e5331000000000000';

    public function testPacking(): void
    {
        $request = new GetValuesResult(array('FCGI_MPXS_CONNS' => 1));
        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => 1), $request->getValues());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = GetValuesResult::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(array('FCGI_MPXS_CONNS' => 1), $request->getValues());
    }
}
