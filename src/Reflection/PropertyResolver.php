<?php

namespace Bluestone\DataTransferObject\Reflection;

use BackedEnum;
use Bluestone\DataTransferObject\Attributes\Map;
use Bluestone\DataTransferObject\Casters\Caster;
use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;
use ReflectionProperty;

class PropertyResolver
{
    public static function set(
        ReflectionProperty $property,
        array $args
    ) {
        $name = $property->getName();

        if ($attributes = $property->getAttributes(Map::class)) {
            $name = $attributes[0]->newInstance()->name;
        }

        if (! isset($args[$name])) {
            return $property->getDefaultValue();
        }

        $value = $args[$name];

        if ($attributes = $property->getAttributes(CastWith::class)) {
            $attribute = $attributes[0]->newInstance();

            /** @var Caster $caster */
            $caster = new $attribute->caster(...$attribute->args);

            return $caster->set($value);
        }

        $type = $property->getType()->getName();

        if (is_subclass_of($type, DataTransferObject::class)) {
            return is_array($value) ? new $type($value) : $value;
        }

        if (is_subclass_of($type, BackedEnum::class)) {
            return $value instanceof BackedEnum ? $value : $type::from($value);
        }

        return $value;
    }

    public static function get(
        object $object,
        ReflectionProperty $property,
    ): mixed {
        $name = $property->getName();

        if ($attributes = $property->getAttributes(Map::class)) {
            $name = $attributes[0]->newInstance()->name;
        }

        $value = $property->getValue($object);

        if ($attributes = $property->getAttributes(CastWith::class)) {
            $attribute = $attributes[0]->newInstance();

            /** @var Caster $caster */
            $caster = new $attribute->caster(...$attribute->args);

            return [$name, $caster->get($value)];
        }

        $type = $property->getType()->getName();

        if (is_subclass_of($type, DataTransferObject::class) && ! is_null($value)) {
            return [$name, $value->toArray()];
        }

        if (is_subclass_of($type, BackedEnum::class) && ! is_null($value)) {
            return [$name, $value->value];
        }

        return [$name, $value];
    }
}
