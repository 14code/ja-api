<?php

namespace I4code\JaApi;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $gatewayMock = $this->createMock(Gateway::class);
        $factoryMock = $this->createMock(Factory::class);

        $repository = new class($gatewayMock, $factoryMock) extends Repository {};

        $this->assertInstanceOf(Repository::class, $repository);
        return $repository;
    }

    /**
     * @param $repository
     * @depends testConstruct
     */
    public function testFindAll($repository)
    {
        $items = $repository->findAll();
        $this->assertIsArray($items);
    }
}
