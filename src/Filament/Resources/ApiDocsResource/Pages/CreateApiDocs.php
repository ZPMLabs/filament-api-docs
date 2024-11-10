<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use InfinityXTech\FilamentApiDocsBuilder\Filament\Actions\CollectionImporterAction;

class CreateApiDocs extends CreateRecord
{
    public static function getResource(): string
    {
        return config('filament-api-docs-builder.resource');
    }

    protected function getHeaderActions(): array
    {
        return [
            CollectionImporterAction::make('importer'),
        ];
    }
}
