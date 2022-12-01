<?php

namespace Bluestone\DataTransferObject;

use ArrayAccess;
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
            $attributes[$property->getName()] = PropertyResolver::get($this, $property);
        }

        return $attributes;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
