<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use InfinityXTech\FilamentApiDocsBuilder\Filament\Actions\CollectionImporterAction;

class ListApiDocs extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-api-docs-builder.resource');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CollectionImporterAction::make('importer'),
        ];
    }
}
