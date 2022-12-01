<?php

namespace Bluestone\DataTransferObject\Reflection;

use BackedEnum;
use Bluestone\DataTransferObject\Casters\Caster;
use Bluestone\DataTransferObject\Casters\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;
use ReflectionProperty;

class PropertyResolver
{
    public static function set(
        ReflectionProperty $property,
        array $args
    ) {
        if (! isset($args[$property->getName()])) {
            return $property->getDefaultValue();
        }

        $value = $args[$property->getName()];

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
        $value = $property->getValue($object);

        if ($attributes = $property->getAttributes(CastWith::class)) {
            $attribute = $attributes[0]->newInstance();

            /** @var Caster $caster */
            $caster = new $attribute->caster(...$attribute->args);

            return $caster->get($value);
        }

        $type = $property->getType()->getName();

        if (is_subclass_of($type, DataTransferObject::class) && ! is_null($value)) {
            return $value->toArray();
        }

        if (is_subclass_of($type, BackedEnum::class) && ! is_null($value)) {
            return $value->value;
        }

        return $value;
    }
}
