<?php

namespace Tests;

use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\Attributes\Map;
use Bluestone\DataTransferObject\Casters\ArrayCaster;
use Bluestone\DataTransferObject\Reflection\PropertyResolver;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Artifacts\FullName;
use Tests\Artifacts\FullNameCaster;
use Tests\Artifacts\Number;

class CanGetPropertyFromResolverTest extends TestCase
{
    /**
     * @test
     * @dataProvider setOfValues
     */
    public function can_get_value($class, $propertyName, $expectations)
    {
        $property = new ReflectionProperty($class, $propertyName);

        $value = PropertyResolver::get($class, $property);

        $this->assertEquals($expectations, $value);
    }

    public function setOfValues(): array
    {
        return [
            "Basic property" => [
                new class { public int $number = 6; },
                'number',
                ['number', 6]
            ],
            "DTO property" => [
                new class { public Number $number; public function __construct() { $this->number = new Number(value: 6); } },
                'number',
                ['number', ['value' => 6]]
            ],
            "Cast property" => [
                new class { #[CastWith(FullNameCaster::class)] public FullName $fullName; public function __construct() { $this->fullName = new FullName('Chris-David', 'Clemovitch'); } },
                'fullName',
                ['fullName', "Chris-David Clemovitch"]
            ],
            "Cast nullable array property" => [
                new class { #[CastWith(ArrayCaster::class, type: Number::class)] public ?array $numbers; },
                'numbers',
                ['numbers', null]
            ],
            "Map property" => [
                new class { #[Map('full_name')] public string $fullName; public function __construct() { $this->fullName = 'Chris-David Clemovitch'; } },
                'fullName',
                ['full_name', "Chris-David Clemovitch"]
            ],
            "Map property with dot notation" => [
                new class { #[Map('address.street')] public string $street; public function __construct() { $this->street = 'New way'; } },
                'street',
                ['address', ['street' => "New way"]],
            ],
        ];
    }
}
