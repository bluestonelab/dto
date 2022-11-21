<?php

namespace Bluestone;

use ArrayAccess;

abstract class DataTransferObject
{
    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        foreach (get_class_vars(static::class) as $key => $value) {
            if (isset($args[$key])) {
                $this->$key = $args[$key];
            }
        }
    }

    public function toArray(): array
    {
        $attributes = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($value instanceof self) {
                $attributes[$key] = $value->toArray();

                continue;
            }

            if (is_array($value) || $value instanceof ArrayAccess) {
                $attributes[$key] = array_map(function ($row) {
                    if ($row instanceof self) {
                        return $row->toArray();
                    }

                    return $row;
                }, $value);

                continue;
            }

            $attributes[$key] = $value;
        }

        return $attributes;
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
