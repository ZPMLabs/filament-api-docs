<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Actions\DeleteAction;
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
            DeleteAction::make(),
        ];
    }
}
