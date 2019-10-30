<?php
declare(strict_types=1);

namespace I4code\JaApi;

use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use function FastRoute\simpleDispatcher;

class Service
{
    protected $middlewares = [];
    protected $routes = [];


    public function delete($route, $handler)
    {
        return $this->addRoute(new Route('DELETE', $route, $handler));
    }


    public function get($route, $handler)
    {
        return $this->addRoute(new Route('GET', $route, $handler));
    }


    public function head($route, $handler)
    {
        return $this->addRoute(new Route('HEAD', $route, $handler));
    }


    public function options($route, $handler)
    {
        return $this->addRoute(new Route('OPTIONS', $route, $handler));
    }


    public function patch($route, $handler)
    {
        return $this->addRoute(new Route('PATCH', $route, $handler));
    }


    public function post($route, $handler)
    {
        return $this->addRoute(new Route('POST', $route, $handler));
    }


    public function put($route, $handler)
    {
        return $this->addRoute(new Route('PUT', $route, $handler));
    }


    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
        return $this;
    }


    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }


    /**
     * @return array
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }


    public function dispatch(ServerRequestInterface $serverRequest)
    {
        $routes = $this->getRoutes();
        $router = simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route->getMethod(), $route->getRoute(), $route->getHandler());
            }
        });

        $dispatcher = new Dispatcher([
            new FastRoute($router),
            new RequestHandler()
        ]);

        return $dispatcher->dispatch($serverRequest);
    }

}