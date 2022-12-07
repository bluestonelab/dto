<?php

namespace Bluestone\DataTransferObject;

use Bluestone\DataTransferObject\Reflection\PropertyResolver;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject implements JsonSerializable
{
    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new ReflectionClass($this);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $value = PropertyResolver::set($property, $args);

            $property->setValue(
                $this,
                $value
            );
        }
    }

    public function toArray(): array
    {
        $attributes = [];

        $class = new ReflectionClass($this);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            list($key, $value) = PropertyResolver::get($this, $property);
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
