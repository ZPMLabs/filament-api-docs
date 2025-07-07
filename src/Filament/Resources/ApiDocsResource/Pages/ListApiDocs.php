<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Actions\CollectionImporterAction;

class ListApiDocs extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-api-docs-builder.resource');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            CollectionImporterAction::make('importer'),
        ];
    }
}
