<?php
declare(strict_types=1);

namespace I4code\JaApi;

class Route
{
    protected $method;
    protected $route;
    protected $handler;

    /**
     * Route constructor.
     * @param string $method
     * @param string $route
     * @param $handler
     */
    public function __construct(string $method, string $route, $handler)
    {
        $this->method = $method;
        $this->route = $route;
        $this->handler = $handler;
    }

    /**
     * @return string|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string|string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

}