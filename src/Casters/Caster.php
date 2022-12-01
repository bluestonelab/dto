<?php

namespace Bluestone\DataTransferObject\Casters;

interface Caster
{
    public function set(mixed $value);

    public function get(mixed $value);
}
