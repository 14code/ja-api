<?php
declare(strict_types=1);

namespace I4code\JaApi;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private $definitions = [];

    public function set($definitions)
    {
        $this->definitions = array_merge($this->definitions, $definitions);
        return $this;
    }


    public function has($id)
    {
        return isset($this->definitions[$id]);
    }


    public function get($id, array $args = [])
    {
        if ($this->has($id)) {
            $runner = $this->definitions[$id];
            return $runner;
        }
        return null;
    }


    public static function new(string $class, array $constructorArgs = [])
    {

        return function () use ($class, $constructorArgs) {
            $constructorArgs = array_map(function ($arg) {
                if (is_callable($arg)) {
                    return $arg();
                }
                return $arg;
            }, $constructorArgs);

            $instance = new $class(...$constructorArgs);
            if (is_callable($instance)) {
                $args = func_get_args();
                return $instance(...$args);
            }
            return $instance;
        };
    }

}