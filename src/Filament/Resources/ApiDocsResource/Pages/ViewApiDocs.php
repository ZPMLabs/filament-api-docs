<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Actions\CollectionDownloaderAction;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Infolists\ApiDocsInfolistBuilder;

class ViewApiDocs extends ViewRecord
{    
    public static function getResource(): string
    {
        return config('filament-api-docs-builder.resource');
    }

    public function infolist(Schema $schema): Schema
    {
        $record = $this->getRecord();

        $this->heading = '[v' . $record->version . '] ' . $record->title;
        $this->subheading = $record->description;

        return $schema->components(ApiDocsInfolistBuilder::make($record));
    }

    protected function getHeaderActions(): array
    {
        return [
            CollectionDownloaderAction::make('downloader'),
        ];
    }
}
