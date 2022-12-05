<?php

namespace Bluestone\DataTransferObject\Attributes;

use Attribute;
use Bluestone\DataTransferObject\Casters\Caster;
use InvalidArgumentException;

#[Attribute]
class CastWith
{
    public array $args;

    public function __construct(public string $caster, mixed ...$args)
    {
        if (! is_subclass_of($this->caster, Caster::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Class %s doesn't implement %s.",
                    $this->caster,
                    Caster::class
                )
            );
        }

        $this->args = $args;
    }
}
