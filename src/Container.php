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


    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }


    public function get($id, array $args = [])
    {
        if (!$this->has($id)) {
            return null; // oder Exception
        }

        $entry = $this->definitions[$id];

        if (is_callable($entry)) {
            // First argument is container to make deps available
            return $entry($this, ...$args);
        }

        return $entry;
    }


    public static function new(string $class, array $constructorArgs = [])
    {

        return function (ContainerInterface $container, ...$runtimeArgs) use ($class, $constructorArgs) {
            $resolved = array_map(function ($arg) use ($container) {
                return is_callable($arg) ? $arg($container) : $arg;
            }, $constructorArgs);

            $instance = new $class(...$resolved);
            
            return is_callable($instance) ? $instance(...$runtimeArgs) : $instance;
        };
    }

}