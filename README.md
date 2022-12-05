# About DTO / Data transfer objects

[![Run tests](https://github.com/bluestonelab/dto/actions/workflows/run_tests.yml/badge.svg)](https://github.com/bluestonelab/dto/actions/workflows/run_tests.yml)
[![Latest Stable Version](https://poser.pugx.org/bluestone/dto/v/stable)](https://packagist.org/packages/bluestone/dto)

## Installation

This package requires `php:^8.1`.  
You can install it via composer:
```bash
composer require bluestone/dto
```

## Usage

This package goal to simplify the build of objects whose serve to pass structured data.  

### Build simple DTO

An exemple of class extends DTO :

```php
use Bluestone\DataTransferObject\DataTransferObject;

class Hooman extends DataTransferObject
{
    public string $name;
}
```

You can instantiate this class like this :

```php
$jane = new Hooman(name: 'Jane');
$john = new Hooman(['name' => 'John']);
```

### Build complex DTO with Casting

An exemple of class with property with casting :

```php
use Bluestone\DataTransferObject\DataTransferObject;
use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\Casters\ArrayCaster;

class Hooman extends DataTransferObject
{
    public string $name;
    
    #[CastWith(ArrayCaster::class, type: Hooman::class)]
    public array $children;
}
```

You can instantiate this class like this :

```php
$jane = new Hooman(
    name: 'Jane', 
    children: [
        new Hooman(name: 'Mario'), 
        new Hooman(name: 'Luigi'),
    ],
);

$john = new Hooman([
    'name' => 'John',
    'children' => [
        ['name' => 'Mario'],
        ['name' => 'Luigi'],
    ],
]);
```

### Build complex DTO with Mapping

An exemple of class with property with mapping :

```php
use Bluestone\DataTransferObject\DataTransferObject;
use Bluestone\DataTransferObject\Attributes\Map;

class Hooman extends DataTransferObject
{
    #[Map('date_of_birth')]
    public string $bornAt;
}
```

You can instantiate this class like this :

```php
$jane = new Hooman(
    date_of_birth: '1970-01-01', 
);

$john = new Hooman([
    'date_of_birth' => '1970-01-01',
]);
```

## Contributing

DTO is an open source project under [MIT License](https://github.com/bluestonelab/dto/blob/master/LICENSE.md) and is [open for contributions](https://github.com/bluestonelab/dto/blob/master/CONTRIBUTING.md).
