<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class JsonEncoderTest extends TestCase
{

    public function testConstructor()
    {
        $encoder = new \I4code\JaApi\JsonEncoder();
        $this->assertInstanceOf(\I4code\JaApi\JsonEncoder::class, $encoder);
    }

    public function testDecode()
    {
        $test = [
            'test' => 'test',
            'tset' => 'tset'
        ];
        $json = json_encode($test);
        $encoder = new \I4code\JaApi\JsonEncoder();
        $this->assertSame($test, $encoder->decode($json));
    }

    public function testEncode()
    {
        $this->assertTrue(true);
    }
}
