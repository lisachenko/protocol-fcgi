<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use PHPUnit\Framework\TestCase;
use Lisachenko\Protocol\FCGI;

/**
 * @author Alexander.Lisachenko
 */
class UnknownTypeTest extends TestCase
{
    protected static string $rawMessage = '010b0000000800002a57544621000000';

    public function testPacking(): void
    {
        $request = new UnknownType(42, 'WTF!');
        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = UnknownType::unpack($binaryData);

        $this->assertEquals(FCGI::UNKNOWN_TYPE, $request->getType());
        $this->assertEquals(42, $request->getUnrecognizedType());
    }
}
