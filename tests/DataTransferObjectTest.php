<?php

namespace Tests;

use Bluestone\DataTransferObject;
use PHPUnit\Framework\TestCase;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function can_instantiate_a_dto()
    {
        $hooman = new class extends DataTransferObject {
            public int $id;
            public string $name;
        };

        $jane = new $hooman(id: 42, name: 'Jane');

        $this->assertEquals(42, $jane->id);
        $this->assertEquals('Jane', $jane->name);
    }
}
