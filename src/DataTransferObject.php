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
            $value = PropertyResolver::handle($property, $args);

            $property->setValue(
                $this,
                $value
            );
        }
    }

    public function toArray(): array
    {
        $attributes = [];

        $properties = array_keys(get_class_vars($this::class));

        foreach ($properties as $property) {
            if (! isset($this->$property)) {
                $attributes[$property] = null;

                continue;
            }

            $value = $this->$property;

            if ($value instanceof self) {
                $attributes[$property] = $value->toArray();

                continue;
            }

            if (is_array($value) || $value instanceof ArrayAccess) {
                $attributes[$property] = array_map(function ($row) {
                    if ($row instanceof self) {
                        return $row->toArray();
                    }

                    return $row;
                }, $value);

                continue;
            }

            $attributes[$property] = $value;
        }

        return $attributes;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
