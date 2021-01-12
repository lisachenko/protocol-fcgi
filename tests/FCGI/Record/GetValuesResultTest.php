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

class GetValuesResultTest extends TestCase
{
    protected static string $rawMessage = '010a0000001206000f01464347495f4d5058535f434f4e4e5331000000000000';

    public function testPacking(): void
    {
        $request = new GetValuesResult(['FCGI_MPXS_CONNS' => '1']);
        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(['FCGI_MPXS_CONNS' => '1'], $request->getValues());

        $this->assertSame(self::$rawMessage, bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin(self::$rawMessage);
        $request    = GetValuesResult::unpack($binaryData);

        $this->assertEquals(FCGI::GET_VALUES_RESULT, $request->getType());
        $this->assertEquals(['FCGI_MPXS_CONNS' => '1'], $request->getValues());
    }
}
