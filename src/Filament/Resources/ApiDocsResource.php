<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Resources;

use InfinityXTech\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;
use InfinityXTech\FilamentApiDocsBuilder\Filament\Forms\ApiDocsFormBuilder;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables;

class ApiDocsResource extends Resource
{
    public static function getModel(): string
    {
        return config('filament-api-docs-builder.model');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(ApiDocsFormBuilder::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiDocs::route('/'),
            'create' => Pages\CreateApiDocs::route('/create'),
            'view' => Pages\ViewApiDocs::route('/{record}'),
            'edit' => Pages\EditApiDocs::route('/{record}/edit'),
        ];
    }
}
