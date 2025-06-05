<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Actions\CollectionDownloaderAction;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Infolists\ApiDocsInfolistBuilder;

class ViewApiDocs extends ViewRecord
{    
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

    protected function getHeaderActions(): array
    {
        return [
            CollectionDownloaderAction::make('downloader'),
        ];
    }
}
