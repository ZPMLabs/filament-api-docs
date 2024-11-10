<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Filament\Actions;

use Filament\Actions\Action;
use InfinityXTech\FilamentApiDocsBuilder\Actions\ImportCollectionAction;
use InfinityXTech\FilamentApiDocsBuilder\Models\ApiDocs;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use InfinityXTech\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\Pages\EditApiDocs;

/**
 * Class CollectionImporterAction
 *
 * A Filament action to import API collections from a JSON file.
 * Uses `ImportCollectionAction` to process the file and save the data
 * into the `ApiDocs` model.
 */
class CollectionImporterAction extends Action
{
    /**
     * Set up the CollectionImporterAction.
     *
     * Configures the action to include a file upload form,
     * process the uploaded file using `ImportCollectionAction`,
     * and store the imported data in the `ApiDocs` model.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Add a file upload field to the action's form
        $this->form([
            FileUpload::make('collection_file') // Create a file upload input
                ->storeFiles(false)            // Avoid storing files in the default storage
        ])
        ->action(function ($data) {
            // Retrieve the uploaded file path
            $filePath = $data['collection_file']->getPathname();

            // Process the file using ImportCollectionAction
            $action = new ImportCollectionAction();
            $params = $action->execute($filePath);

            // Save the imported data as a new ApiDocs record
            $doc = ApiDocs::create($params);

            // Notify the user of successful import
            Notification::make()
                ->title(__('Documentation collection was imported successfully!'))
                ->success()
                ->send();

            // Redirect to the EditApiDocs page for the newly created record
            redirect()->to(EditApiDocs::getUrl([$doc]));
        })
        ->label(__('Import Collection JSON')); // Set the label for the action
    }
}