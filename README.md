# This is my package filament-api-docs-builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/infinityxtech/filament-api-docs-builder.svg?style=flat-square)](https://packagist.org/packages/infinityxtech/filament-api-docs-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/infinityxtech/filament-api-docs-builder/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/infinityxtech/filament-api-docs-builder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/infinityxtech/filament-api-docs-builder/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/infinityxtech/filament-api-docs-builder/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/infinityxtech/filament-api-docs-builder.svg?style=flat-square)](https://packagist.org/packages/infinityxtech/filament-api-docs-builder)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require infinityxtech/filament-api-docs-builder
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-api-docs-builder-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-api-docs-builder-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-api-docs-builder-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentApiDocsBuilder = new InfinityXTech\FilamentApiDocsBuilder();
echo $filamentApiDocsBuilder->echoPhrase('Hello, InfinityXTech!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [InfinityXTech](https://github.com/infinityxtech)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
