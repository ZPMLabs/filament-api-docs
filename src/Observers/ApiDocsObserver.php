<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Observers;

use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Model;
use InfinityXTech\FilamentApiDocsBuilder\Models\ApiDocs;

class ApiDocsObserver
{    
    /**
     * Handle the ApiDocs "creating" event.
     */
    public function creating(ApiDocs $doc): void
    {        
        $doc->slug = str($doc->title . ' ' . now())->slug();
        
        if (config('filament-api-docs-builder.save_current_user') && auth()->user()) {
            $doc->user_id = auth()->user()->id();
        }
        
        if ($tenantClass = config('filament-api-docs-builder.tenant')) {
            if (class_exists($tenantClass) && method_exists($tenantClass, 'getTenant')) {
                $tenant = (new $tenantClass)->getTenant();

                if ($tenant instanceof Model) {
                    $doc->tenant_id = $tenant->id;
                }
            }
        }
    }
}
