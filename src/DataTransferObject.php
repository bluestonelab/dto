<?php

namespace Bluestone\DataTransferObject;

use ArrayAccess;
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
            $propertyClass = $property->getType()->getName();
            $propertyName = $property->getName();

            if (! isset($args[$propertyName])) {
                continue;
            }

            $value = $args[$propertyName];

            if (is_subclass_of($propertyClass, self::class)) {
                $value = is_array($value) ? new $propertyClass($value) : $value;
                $this->$propertyName = $value;
                continue;
            }

            $this->$propertyName = $value;
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
