<?php
declare(strict_types=1);

namespace I4code\JaApi;


abstract class Repository
{
    protected $repository;

    protected $gateway;
    protected $factory;

    public function __construct(Gateway $gateway, Factory $factory)
    {
        $this->gateway = $gateway;
        $this->factory = $factory;
    }

    public function get()
    {
        return $this->getRepository();
    }


    /**
     * @return array
     */
    public function getRepository()
    {
        if (null === $this->repository) {
            $this->setRepository($this->findAll());
        }
        return $this->repository;
    }


    /**
     * @param array $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
        return $this;
    }


    public function findAll(): array
    {
        $results = $this->gateway->retrieveAll();

        if (!is_array($results)) {
            throw new InvalidResultsetException("Resultset is " . gettype($results) . " instead of array");
        }

        $items = [];

        foreach ($results as $result) {
            if (is_array($result)) {
                $item = $this->factory->createFromArray($result);
            } else {
                $item = $this->factory->create($result);
            }
            $items[] = $item;
        }

        return $items;
    }

}