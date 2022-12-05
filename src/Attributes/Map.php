<?php

namespace Bluestone\DataTransferObject\Attributes;

use Attribute;

#[Attribute]
class Map
{
    public function __construct(
        public string $name
    ) {
    }
}
