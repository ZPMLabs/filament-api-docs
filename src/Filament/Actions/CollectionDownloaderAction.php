<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Actions;

use Filament\Actions\Action;
use InfinityXTech\FilamentApiDocsBuilder\Actions\ExportJsonAction;

/**
 * Class CollectionDownloaderAction
 *
 * A Filament action that allows users to download API collections
 * in JSON format by exporting them through the `ExportJsonAction`.
 */
class CollectionDownloaderAction extends Action
{
    /**
     * Sets up the CollectionDownloaderAction.
     *
     * Defines the action's behavior, including invoking the `ExportJsonAction`
     * to generate and download the JSON collection.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Define the action to be executed
        $this->action(fn($record) => (new ExportJsonAction)->execute(
            $record->title,        // Pass the title of the record
            $record->description,  // Pass the description of the record
            $record->data          // Pass the collection data of the record
        ))
        ->label(__('Download Collection JSON')); // Set the label for the action
    }
}