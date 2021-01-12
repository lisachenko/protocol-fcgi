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

class GetValuesTest extends TestCase
{
    protected static string $rawMessage = '01090000001107000f00464347495f4d5058535f434f4e4e5300000000000000';

    public function testPacking(): void
    {
        $request = new GetValues(['FCGI_MPXS_CONNS']);
        $this->assertEquals(FCGI::GET_VALUES, $request->getType());
        $this->assertEquals(['FCGI_MPXS_CONNS' => ''], $request->getValues());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = GetValues::unpack($binaryData);

        $this->assertEquals(FCGI::GET_VALUES, $request->getType());
        $this->assertEquals(['FCGI_MPXS_CONNS' => ''], $request->getValues());
    }
}
