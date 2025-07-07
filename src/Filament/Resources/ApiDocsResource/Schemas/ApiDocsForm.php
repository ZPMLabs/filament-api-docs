<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Schemas;

use ZPMLabs\FilamentApiDocsBuilder\Filament\Forms\ApiDocsFormBuilder;
use Filament\Schemas\Schema;

class ApiDocsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(ApiDocsFormBuilder::make());
    }
}
