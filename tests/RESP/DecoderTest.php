<?php

namespace RedStor\Tests\RESP;

use RedStor\Client\Decoder;
use ⌬\Tests\TestCase;

/**
 * @internal
 * @covers \RedStor\Client\Decoder
 */
class DecoderTest extends TestCase
{
    /** @var Decoder */
    protected $decoder;

    public function setUp(): void
    {
        parent::setUp();
        $this->decoder = new Decoder();
    }

    public function dataproviderBulkStrings()
    {
        return [
            ['foo', "$3\r\nfoo\r\n"],
        ];
    }

    public function dataproviderArraysOfBulkStrings()
    {
        return [
            [['foo', 'bar', 'baz'],       "*3\r\n$3\r\nfoo\r\n$3\r\nbar\r\n$3\r\nbaz\r\n"],
            [['PING'],                  "*1\r\n$4\r\nPING\r\n"],
            [['PING', 'teststring'],    "*2\r\n$4\r\nPING\r\n$10\r\nteststring\r\n"],
            [['PING', 'arse'],          "*2\r\n$4\r\nPING\r\n$4\r\narse\r\n"],
            [['LLEN', 'mylist'],        "*2\r\n$4\r\nLLEN\r\n$6\r\nmylist\r\n"],
        ];
    }

    /**
     * @dataProvider dataproviderBulkStrings
     *
     * @param mixed $expected
     * @param mixed $respBulkString
     */
    public function testDecoderBulkString($expected, $respBulkString)
    {
        $this->assertEquals($expected, $this->decoder->decode($respBulkString));
    }

    /**
     * @dataProvider dataproviderArraysOfBulkStrings
     *
     * @param mixed $expected
     * @param mixed $respArrayOfBulkString
     */
    public function testDecoderArrayOfBulkString($expected, $respArrayOfBulkString)
    {
        $this->assertEquals($expected, $this->decoder->decode($respArrayOfBulkString));
    }
}
