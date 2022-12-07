<?php

namespace Tests;

use Bluestone\DataTransferObject\Attributes\Map;
use Bluestone\DataTransferObject\DataTransferObject;
use Bluestone\DataTransferObject\Reflection\PropertyResolver;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Artifacts\FullName;
use Tests\Artifacts\FullNameCaster;
use Tests\Artifacts\Number;
use Bluestone\DataTransferObject\Attributes\CastWith;

class CanSetPropertyFromResolverTest extends TestCase
{
    /**
     * @test
     * @dataProvider setOfValues
     */
    public function can_set_value($class, $propertyName, $args, $expectations)
    {
        $property = new ReflectionProperty($class, $propertyName);

        $value = PropertyResolver::set($property, $args);

        foreach ($expectations as $key => $expected) {
            if (is_object($value) && is_string($key)) {
                $this->assertEquals($expected, $value->$key);

                continue;
            }

            $this->assertEquals($expected, $value);
        }
    }

    public function setOfValues(): array
    {
        return [
            "Basic property" => [
                new class { public int $number; },
                'number',
                ['number' => 6],
                [6],
            ],
            "Dto property" => [
                new class { public Number $number; },
                'number',
                ['number' => new Number(value: 2)],
                ['value' => 2],
            ],
            "Dto property from array" => [
                new class { public Number $number; },
                'number',
                ['number' => ['value' => 6]],
                ['value' => 6],
            ],
            "Cast property" => [
                new class { #[CastWith(FullNameCaster::class)] public FullName $fullName; },
                'fullName',
                ['fullName' => "Chris Rooky"],
                ['firstname' => "Chris", 'lastname' => "Rooky"],
            ],
            "Map property" => [
                new class { #[Map('full_name')] public string $fullName; },
                'fullName',
                ['full_name' => "Chris Rooky"],
                ["Chris Rooky"],
            ],
            "Map property with dot notation" => [
                new class { #[Map('address.street')] public string $street; },
                'street',
                ['address' => ['street' => "New way"]],
                ["New way"],
            ],
        ];
    }
}
