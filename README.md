![image](https://github.com/user-attachments/assets/d3ba9e91-6998-4a8c-b815-eacb0729c3a8)

# Filament Api Docs Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/infinityxtech/filament-api-docs-builder.svg?style=flat-square)](https://packagist.org/packages/infinityxtech/filament-api-docs-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/infinityxtech/filament-api-docs-builder/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/infinityxtech/filament-api-docs-builder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/infinityxtech/filament-api-docs-builder/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/infinityxtech/filament-api-docs-builder/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/infinityxtech/filament-api-docs-builder.svg?style=flat-square)](https://packagist.org/packages/infinityxtech/filament-api-docs-builder)

This package allows you to build a good looking and functional api documentation. Including exporting and importing actions with postman standard.

## Installation

You can install the package via composer repositories:

```json
"repositories": [
    {
        "type": "vsc",
        "url": "https://github.com/InfinityXTech/filament-api-docs"
    }
]
```

```bash
composer require infinityxtech/filament-api-docs-builder
```

You can install the package with:

```bash
php artisan filament-api-docs-builder:install
```

Otherwise you can publish and run the migrations with:

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


## Usage

You can use this package by registering the plugin inside you filament service provider.

```php
->plugin(InfinityXTech\FilamentApiDocsBuilder\FilamentApiDocsBuilderPlugin::make())
```

Otherwise you can make your own resource and `ApiDocsFormBuilder` for form generation.

```php
use InfinityXTech\FilamentApiDocsBuilder\Filament\Forms\ApiDocsFormBuilder;

public static function getModel(): string
{
    return config('filament-api-docs-builder.model');
}

public static function form(Form $form): Form
{
    return $form->schema(ApiDocsFormBuilder::make());
}
```

And `ApiDocsInfolistBuilder` for infolist generation.

```php

use InfinityXTech\FilamentApiDocsBuilder\Filament\Infolists\ApiDocsInfolistBuilder;

public static function getResource(): string
{
    return config('filament-api-docs-builder.resource');
}

public function infolist(Infolist $infolist): Infolist
{
    $record = $this->getRecord();

    $this->heading = '[v' . $record->version . '] ' . $record->title;
    $this->subheading = $record->description;

    return $infolist->schema(ApiDocsInfolistBuilder::make($record));
}
```

There are also two actions for export and import docs with postman json standard.

```php
use InfinityXTech\FilamentApiDocsBuilder\Filament\Actions\CollectionDownloaderAction;
use InfinityXTech\FilamentApiDocsBuilder\Filament\Actions\CollectionImporterAction;

protected function getHeaderActions(): array
{
    return [
        CollectionDownloaderAction::make('downloader'),
        CollectionImporterAction::make('importer'),
    ];
}
```

This package includes various different code builders but you can add your own in config `code_builders` array.
You can also predefine your enpoint parameter in config `predefined_params` array. [Check config for more details]

If you are using multi tenancy you need to set your tenant model class in config with method `getTenant`.

If you want to use infolist publicly, just make a public filament page and pass in infolist.

## Screenshots

![image](https://github.com/user-attachments/assets/e8183f56-a001-48ba-8127-74a6478c9bcb)
![image](https://github.com/user-attachments/assets/fbee6f2e-1dec-4487-9cc0-05e659170f3d)
![image](https://github.com/user-attachments/assets/ae1f621e-0b31-4c62-9fb3-3a85b1942346)


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

Proprietary license. Please see [License File](LICENSE.md) for more information.
