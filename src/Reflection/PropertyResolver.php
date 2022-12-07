<?php

namespace Bluestone\DataTransferObject\Reflection;

use BackedEnum;
use Bluestone\Collection\Arr;
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

        $value = Arr::get($args, $name, $property->getDefaultValue());

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
            return $value instanceof BackedEnum || is_null($value) ? $value : $type::from($value);
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

        $value = $property->isInitialized($object) ? $property->getValue($object) : null;

        $type = $property->getType()->getName();

        if ($attributes = $property->getAttributes(CastWith::class)) {
            $attribute = $attributes[0]->newInstance();

            /** @var Caster $caster */
            $caster = new $attribute->caster(...$attribute->args);

            $value = $caster->get($value);
        }

        if (is_subclass_of($type, DataTransferObject::class) && ! is_null($value)) {
            $value = $value->toArray();
        }

        if (is_subclass_of($type, BackedEnum::class) && ! is_null($value)) {
            $value = $value->value;
        }

        if (str_contains($name, '.')) {
            $fragments = array_reverse(explode('.', $name));
            $name = array_pop($fragments);

            foreach ($fragments as $key) {
                $value = [$key => $value];
            }
        }

        return [$name, $value];
    }
}
