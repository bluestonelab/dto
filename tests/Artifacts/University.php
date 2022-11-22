<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\DataTransferObject;

class University extends DataTransferObject
{
    public string $name;
    public array $students = [];
}
