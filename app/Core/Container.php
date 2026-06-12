<?php
namespace App\Core;

use ReflectionClass;

class Container
{
    private static ?self $instance = null;
    private array $bindings = [];
    private array $instances = [];

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function () use ($abstract, $factory) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $factory();
            }
            return $this->instances[$abstract];
        };
    }

    public function make(string $class): object
    {
        if (isset($this->bindings[$class])) {
            return ($this->bindings[$class])();
        }

        $ref = new ReflectionClass($class);
        $constructor = $ref->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $args[] = $this->make($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Não foi possível resolver dependência: {$param->getName()}");
            }
        }

        return $ref->newInstanceArgs($args);
    }
}
