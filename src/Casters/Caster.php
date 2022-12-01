<?php

namespace Bluestone\DataTransferObject\Casters;

interface Caster
{
    public function cast(mixed $value);
}
