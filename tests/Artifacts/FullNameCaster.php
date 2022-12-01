<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\Casters\Caster;

class FullNameCaster implements Caster
{
    public function set(mixed $value): FullName
    {
        preg_match('/([a-zA-Z-]+)\s(.*)/', $value, $matches);

        return new FullName(firstname: $matches[1], lastname: $matches[2]);
    }

    public function get(mixed $value): string
    {
        return sprintf("%s %s", $value->firstname, $value->lastname);
    }
}
