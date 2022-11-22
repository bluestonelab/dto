# Contributing

Welcome et thanks to contribute to this project.  
First, please describe your needs in a new [issue](https://github.com/bluestonelab/dto/issues).

## How to write code

1. We respect [PSR-1](https://www.php-fig.org/psr/psr-1/) et [PSR-12](https://www.php-fig.org/psr/psr-12/)
2. Test your code
3. Make small code for easy review

## How to run test

```shell
docker run --rm -it -w /app -v $PWD:/app webdevops/php:8.1 composer install
docker run --rm -it -w /app -v $PWD:/app webdevops/php:8.1 composer test
```