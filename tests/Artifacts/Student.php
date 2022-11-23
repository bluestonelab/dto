<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\DataTransferObject;

class Student extends DataTransferObject
{
    public string $name;
    public ?Gender $gender;
    public ?Skill $skill = null;
    public array $ratings = [];
}
