<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use ZPMLabs\FilamentApiDocsBuilder\Observers\ApiDocsObserver;

#[ObservedBy(ApiDocsObserver::class)]
class ApiDocs extends Model
{
    protected $fillable = [
        'title',
        'description',
        'version',
        'data',
        'user_id',
        'tenant_id',
    ];

    protected $casts = ['data' => 'array'];
}
