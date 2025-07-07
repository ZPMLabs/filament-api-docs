<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages\CreateApiDocs;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages\EditApiDocs;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages\ListApiDocs;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages\ViewApiDocs;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Schemas\ApiDocsForm;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Tables\ApiDocsTable;

class ApiDocsResource extends Resource
{
    public static function getModel(): string
    {
        return config('filament-api-docs-builder.model');
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return config('filament-api-docs-builder.resource_icon');
    }

    public static function form(Schema $schema): Schema
    {
        return ApiDocsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiDocsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiDocs::route('/'),
            'create' => CreateApiDocs::route('/create'),
            'view' => ViewApiDocs::route('/{record}'),
            'edit' => EditApiDocs::route('/{record}/edit'),
        ];
    }
}
