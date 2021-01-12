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
class BeginRequestTest extends TestCase
{
    protected static string $rawMessage = '01010000000800000001010000000000';

    public function testPacking(): void
    {
        $request = new BeginRequest(FCGI::RESPONDER, FCGI::KEEP_CONN);
        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = BeginRequest::unpack($binaryData);

        $this->assertEquals(FCGI::BEGIN_REQUEST, $request->getType());
        $this->assertEquals(FCGI::RESPONDER, $request->getRole());
        $this->assertEquals(FCGI::KEEP_CONN, $request->getFlags());
    }
}
