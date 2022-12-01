<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\Casters\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;

class Student extends DataTransferObject
{
    #[CastWith(FullNameCaster::class)]
    public FullName $fullName;
    public ?Gender $gender;
    public ?Skill $skill = null;
    public array $ratings = [];
}
