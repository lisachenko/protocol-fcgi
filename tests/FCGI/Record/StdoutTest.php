<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class StdoutTest extends TestCase
{
    protected static $rawMessage = '01060000000404007465737400000000';

    public function testPacking(): void
    {
        $request = new Stdout('test');
        $this->assertEquals($request->getContentData(), 'test');
        $this->assertEquals($request->getType(), FCGI::STDOUT);
        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = Stdout::unpack(hex2bin(self::$rawMessage));
        $this->assertEquals($request->getType(), FCGI::STDOUT);
        $this->assertEquals($request->getContentData(), 'test');
    }
}
