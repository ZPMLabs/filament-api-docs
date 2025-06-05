<?php

namespace ZPMLabs\FilamentApiDocsBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentApiDocsBuilderPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-api-docs-builder';
    }

    public function register(Panel $panel): void
    {
        $panel->resources(array_filter([
            config('filament-api-docs-builder.resource'),
        ]));
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
