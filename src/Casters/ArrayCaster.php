<?php

namespace Bluestone\DataTransferObject\Casters;

use Bluestone\DataTransferObject\DataTransferObject;
use InvalidArgumentException;

class ArrayCaster implements Caster
{
    public function __construct(public string $type)
    {
    }

    public function set(mixed $value)
    {
        if (is_null($value)) {
            return null;
        }

        return array_map(function ($item) {
            if ($item instanceof $this->type) {
                return $item;
            }

            if (is_array($item)) {
                return new $this->type(...$item);
            }

            throw new InvalidArgumentException(
                sprintf(
                    "ArrayCaster expect items of type array or specified item type %s.",
                    $this->type
                )
            );
        }, $value);
    }

    public function get(mixed $value)
    {
        if (is_null($value)) {
            return null;
        }

        return array_map(function ($item) {
            if ($item instanceof DataTransferObject) {
                return $item->toArray();
            }

            return $item;
        }, $value);
    }
}
