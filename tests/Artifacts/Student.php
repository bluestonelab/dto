<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\DataTransferObject;

class Student extends DataTransferObject
{
    public string $name;
    public ?Skill $skill = null;
    public array $ratings = [];
}
