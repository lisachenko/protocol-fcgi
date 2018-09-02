<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class DataTest extends TestCase
{
    protected static $rawMessage = '01080000000404007465737400000000';

    public function testPacking(): void
    {
        $request = new Data('test');
        $this->assertEquals('test', $request->getContentData());
        $this->assertEquals(FCGI::DATA, $request->getType());
        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = Data::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals(FCGI::DATA, $request->getType());
        $this->assertEquals('test', $request->getContentData());
    }
}
