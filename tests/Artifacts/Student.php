<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\Attributes\Map;
use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;

class Student extends DataTransferObject
{
    #[Map('full_name')]
    #[CastWith(FullNameCaster::class)]
    public FullName $fullName;
    public ?Gender $gender;
    public ?Skill $skill = null;
    public array $ratings = [];
}
