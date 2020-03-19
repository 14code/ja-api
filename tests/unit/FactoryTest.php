<?php


use I4code\JaApi\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    protected $factory;

    public function setUp(): void
    {
        $this->factory = new class implements Factory {

            public function create($data = null)
            {
                return (object) [];
            }

            public function createFromArray(array $data)
            {
                return (object) [];
            }

            public function createFromObject(object $data)
            {
                return (object) [];
            }

        };
    }

    public function testCreate()
    {
        $item = $this->factory->create();
        $this->assertIsObject($item);
    }

    public function testCreateFromArray()
    {
        $item = $this->factory->createFromArray([]);
        $this->assertIsObject($item);
    }

    public function testCreateFromObject()
    {
        $item = $this->factory->createFromObject((object) []);
        $this->assertIsObject($item);
    }
}
