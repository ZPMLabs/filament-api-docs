<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \InfinityXTech\FilamentApiDocsBuilder\FilamentApiDocsBuilder
 */
class FilamentApiDocsBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \InfinityXTech\FilamentApiDocsBuilder\FilamentApiDocsBuilder::class;
    }
}
