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

## Contributing

DTO is an open source project under MIT License and is open for contributions.