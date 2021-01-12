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
class StderrTest extends TestCase
{
    protected static string $rawMessage = '01070000000404007465737400000000';

    public function testPacking(): void
    {
        $request = new Stderr('test');
        $this->assertEquals('test', $request->getContentData());
        $this->assertEquals(FCGI::STDERR, $request->getType());
        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = Stderr::unpack($binaryData);
        $this->assertEquals(FCGI::STDERR, $request->getType());
        $this->assertEquals('test', $request->getContentData());
    }
}
