<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Enum CodeStyle
 *
 * Represents various code styles supported in the API documentation builder.
 * Implements the HasLabel contract to provide user-friendly labels for each style.
 */
enum CodeStyle: string implements HasLabel
{
    case BLADE = 'blade';
    case CSS = 'css';
    case GDSCRIPT = 'gdscript';
    case HTML = 'html';
    case JAVASCRIPT = 'javascript';
    case JSON = 'json';
    case PHP = 'php';
    case SQL = 'sql';
    case TWIG = 'twig';
    case XML = 'xml';
    case YAML = 'yaml';

    /**
     * Get the title-cased version of the language for display purposes.
     *
     * @return string|null The user-friendly label for the code style.
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::BLADE => 'Blade',
            self::CSS => 'CSS',
            self::GDSCRIPT => 'GDScript',
            self::HTML => 'HTML',
            self::JAVASCRIPT => 'JavaScript',
            self::JSON => 'JSON',
            self::PHP => 'PHP',
            self::SQL => 'SQL',
            self::TWIG => 'Twig',
            self::XML => 'XML',
            self::YAML => 'YAML',
        };
    }
}