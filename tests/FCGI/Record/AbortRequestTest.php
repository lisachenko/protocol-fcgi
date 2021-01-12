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
class AbortRequestTest extends TestCase
{
    protected static string $rawMessage = '0102000100000000';

    public function testPacking(): void
    {
        $request = new AbortRequest(1);
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = AbortRequest::unpack($binaryData);
        $this->assertEquals(FCGI::ABORT_REQUEST, $request->getType());
        $this->assertEquals(1, $request->getRequestId());
    }
}
