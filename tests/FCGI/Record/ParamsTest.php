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

use Lisachenko\Protocol\FCGI\RecordType;
use PHPUnit\Framework\TestCase;

class ParamsTest extends TestCase
{
    protected static string $rawMessage ='
        01040000005b05000f0e5343524950545f46494c454e414d452f686f6d652f746573742e7068701
        107474154455741595f494e544552464143454347492f312e310f115345525645525f534f465457
        4152455048502f50726f746f636f6c2d464347490000000000';

    /**
     * @var array<string, string>
     */
    protected static array $params = [
        'SCRIPT_FILENAME'   => '/home/test.php',
        'GATEWAY_INTERFACE' => 'CGI/1.1',
        'SERVER_SOFTWARE'   => 'PHP/Protocol-FCGI',
    ];

    public function testPacking(): void
    {
        $request = new Params(self::$params);
        $this->assertSame(RecordType::Params, $request->getType());
        $this->assertEquals(self::$params, $request->getValues());

        $this->assertSame(preg_replace('/\s+/', '', self::$rawMessage), bin2hex((string) $request));
    }

    public function testUnpacking(): void
    {
        $oneLineData = preg_replace('/\s+/', '', self::$rawMessage) ?? '';
        $binaryData  = hex2bin($oneLineData);
        if ($binaryData === false) {
            throw new \ValueError('Invalid binary string format');
        }
        $request = Params::unpack($binaryData);

        $this->assertSame(RecordType::Params, $request->getType());
        $this->assertEquals(self::$params, $request->getValues());
    }

    /**
     * Names and values over 127 bytes use the long (4-byte) length form with the top bit set
     */
    public function testLongNameValueRoundTrip(): void
    {
        $name  = str_repeat('N', 150);
        $value = str_repeat('V', 200);
        $request = new Params([$name => $value, 'SHORT' => 'yes']);

        $restored = Params::unpack((string) $request);

        $this->assertSame([$name => $value, 'SHORT' => 'yes'], $restored->getValues());
    }

    /**
     * An empty FCGI_PARAMS record marks the end of the parameter stream and is
     * hydrated without running the constructor
     */
    public function testEmptyParamsRecordHasNoValues(): void
    {
        /** @var string $binaryData */
        $binaryData = hex2bin('0104000100000000');
        $record     = Params::unpack($binaryData);

        $this->assertSame(RecordType::Params, $record->getType());
        $this->assertSame([], $record->getValues());
    }
}
