<?php
/**
 * @author Alexander.Lisachenko
 * @date   23.10.2015
 */

namespace Protocol\FCGI;

use Protocol\FCGI;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;

class FrameParserTest extends \PHPUnit_Framework_TestCase
{
    public function testHasFrame()
    {
        $incompletePacket = hex2bin('010100010008000000');
        $this->assertFalse(FrameParser::hasFrame($incompletePacket));

        $completePacket = hex2bin('01010001000800000001010000000000');
        $this->assertTrue(FrameParser::hasFrame($completePacket));
    }

    public function testParsingFrame()
    {
        // one FCGI_BEGIN request with two empty FCGI_PARAMS request
        $dataStream = hex2bin('0101000100080000000101000000000001040001000000000104000100000000');
        $bufferSize = strlen($dataStream);
        $this->assertEquals(32, $bufferSize);

        // consume FCGI_BEGIN request
        $record = FrameParser::parseFrame($dataStream);
        $this->assertInstanceOf(BeginRequest::class, $record);
        $recordSize = strlen($record);
        $this->assertEquals(16, $recordSize);

        $this->assertEquals($bufferSize - $recordSize, strlen($dataStream));

        // consume first FCGI_PARAMS request
        $record = FrameParser::parseFrame($dataStream);
        $this->assertInstanceOf(Params::class, $record);

        // consume second FCGI_PARAMS request
        $record = FrameParser::parseFrame($dataStream);
        $this->assertInstanceOf(Params::class, $record);

        $this->assertEquals(0, strlen($dataStream));
    }
}