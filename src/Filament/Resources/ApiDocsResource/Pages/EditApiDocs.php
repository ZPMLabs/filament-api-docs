<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiDocs extends EditRecord
{
    public static function getResource(): string
    {
        return config('filament-api-docs-builder.resource');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
