<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\Casters\ArrayCaster;
use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;

class University extends DataTransferObject
{
    public string $name;

    #[CastWith(ArrayCaster::class, type: Student::class)]
    public array $students = [];
}
