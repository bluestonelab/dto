<?php

namespace Tests;

use Bluestone\DataTransferObject\DataTransferObject;
use Bluestone\DataTransferObject\Reflection\PropertyResolver;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Artifacts\FullName;
use Tests\Artifacts\FullNameCaster;
use Tests\Artifacts\Number;
use Bluestone\DataTransferObject\Casters\CastWith;

class PropertyResolverTest extends TestCase
{
    /** @test */
    public function can_set_basic_property()
    {
        $class = new class {
            public int $number;
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::set($property, ['number' => 6]);

        $this->assertEquals(6, $value);
    }

    /** @test */
    public function can_set_dto_property()
    {
        $class = new class {
            public Number $number;
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::set($property, ['number' => ['value' => 6]]);

        $this->assertInstanceOf(Number::class, $value);
        $this->assertEquals(6, $value->value);

        $value = PropertyResolver::set($property, ['number' => new Number(value: 2)]);

        $this->assertInstanceOf(Number::class, $value);
        $this->assertEquals(2, $value->value);
    }

    /** @test */
    public function can_set_property_with_cast()
    {
        $class = new class {
            #[CastWith(FullNameCaster::class)]
            public FullName $fullName;
        };

        $property = new ReflectionProperty($class, 'fullName');

        $value = PropertyResolver::set($property, ['fullName' => "Chris Rooky"]);

        $this->assertInstanceOf(FullName::class, $value);
        $this->assertEquals("Chris", $value->firstname);
        $this->assertEquals("Rooky", $value->lastname);
    }

    /** @test */
    public function can_get_basic_property()
    {
        $class = new class {
            public int $number;

            public function __construct()
            {
                $this->number = 6;
            }
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::get($class, $property);

        $this->assertEquals(6, $value);
    }

    /** @test */
    public function can_get_dto_property()
    {
        $class = new class {
            public Number $number;

            public function __construct()
            {
                $this->number = new Number(value: 6);
            }
        };

        $property = new ReflectionProperty($class, 'number');

        $value = PropertyResolver::get($class, $property);

        $this->assertEquals(['value' => 6], $value);
    }

    /** @test */
    public function can_get_property_with_cast()
    {
        $class = new class {
            #[CastWith(FullNameCaster::class)]
            public FullName $fullName;

            public function __construct()
            {
                $this->fullName = new FullName('Chris-David', 'Clemovitch');
            }
        };

        $property = new ReflectionProperty($class, 'fullName');

        $value = PropertyResolver::get($class, $property);

        $this->assertEquals("Chris-David Clemovitch", $value);
    }
}
