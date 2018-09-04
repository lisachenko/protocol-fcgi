<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class UnknownTypeTest extends TestCase
{
    protected static $rawMessage = '010b0000000800002a57544621000000';

    public function testPacking(): void
    {
        $request = new UnknownType(42, 'WTF!');
        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $request = UnknownType::unpack(hex2bin(self::$rawMessage));

        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());
    }
}
