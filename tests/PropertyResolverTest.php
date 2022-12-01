<?php

namespace Tests;

use Bluestone\DataTransferObject\Reflection\PropertyResolver;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Artifacts\Number;
use Tests\Artifacts\AmountCaster;
use Bluestone\DataTransferObject\Casters\CastWith;

class PropertyResolverTest extends TestCase
{
    /** @test */
    public function can_handle_basic_property()
    {
        $class = new class {
            public int $number;
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::handle($property, ['number' => 6]);

        $this->assertEquals(6, $value);
    }

    /** @test */
    public function can_handle_dto_property()
    {
        $class = new class {
            public Number $number;
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::handle($property, ['number' => ['value' => 6]]);

        $this->assertInstanceOf(Number::class, $value);
        $this->assertEquals(6, $value->value);

        $value = PropertyResolver::handle($property, ['number' => new Number(value: 2)]);

        $this->assertInstanceOf(Number::class, $value);
        $this->assertEquals(2, $value->value);
    }

    /** @test */
    public function can_handle_property_with_cast()
    {
        $class = new class {
            #[CastWith(AmountCaster::class)]
            public float $amount;
        };

        $property = new ReflectionProperty($class, 'amount');

        $value = PropertyResolver::handle($property, ['amount' => "3\xc2\xa0000,45\xc2\xa0€"]);

        $this->assertEquals(3000.45, $value);
    }
}
