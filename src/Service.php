<?php
declare(strict_types=1);

namespace I4code\JaApi;

use FastRoute\RouteCollector;
use I4code\JaApi\Middleware\CorsMiddleware;
use I4code\JaApi\Middleware\EnforceJsonMiddleware;
use LogicException;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use function FastRoute\simpleDispatcher;


class Service
{
    protected $middlewares = [];
    protected $routes = [];
    
    public function __construct(
        protected ContainerInterface $container
    ) {}


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

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return Service
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
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


    /**
     * @return array
     */
    public function createMiddlewareStack()
    {
        $container = $this->getContainer();
        $middlewares = [];
        
        $middlewareIds = [
            CorsMiddleware::class,
            EnforceJsonMiddleware::class,
        ];
        
        foreach ($middlewareIds as $middlewareId) {
            $middleware = $this->resolveMiddleware($middlewareId);
            if ($middleware instanceof MiddlewareInterface) {
                $middlewares[] = $middleware;
            }
        }
        
        return array_merge(
            $middlewares,
            [new FastRoute($this->createRouter())],
            $this->middlewares,
            [new RequestHandler($container)]
        );
    }


    /**
     * @return \FastRoute\Dispatcher
     */
    public function createRouter()
    {
        $routes = $this->getRoutes();
        return simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route->getMethod(), $route->getRoute(), $route->getHandler());
            }
        });
    }


    /**
     * @param ServerRequestInterface $serverRequest
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $serverRequest)
    {
        $dispatcher = new Dispatcher($this->createMiddlewareStack());
        return $dispatcher->dispatch($serverRequest);
    }


    protected function resolveMiddleware(string $id): MiddlewareInterface
    {
        $container = $this->getContainer();
        
        if ($container && $container->has($id)) {
            $middleware = $container->get($id);
            
            if (! $middleware instanceof MiddlewareInterface) {
                throw new LogicException("Container entry [$id] is not a MiddlewareInterface.");
            }
            
            return $middleware;
        }

        if (! class_exists($id)) {
            throw new LogicException("Middleware class [$id] not found and not registered in container.");
        }
        
        $middleware = new $id();
        
        if (! $middleware instanceof MiddlewareInterface) {
            throw new LogicException("Class [$id] does not implement MiddlewareInterface.");
        }
        
        return $middleware;
    }

}