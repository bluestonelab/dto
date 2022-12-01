<?php

namespace Bluestone\DataTransferObject\Reflection;

use BackedEnum;
use Bluestone\DataTransferObject\Casters\Caster;
use Bluestone\DataTransferObject\Casters\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;
use ReflectionProperty;

class PropertyResolver
{
    public static function handle(
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

            return $caster->cast($value);
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
}
