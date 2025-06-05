<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Enums;

/**
 * Enum PredefinedCodeBuilders
 *
 * Represents a set of predefined code builders available for generating
 * API request examples in various programming languages.
 */
enum PredefinedCodeBuilders: string
{
    case cURL = 'cURL';
    case PHP = 'PHP';
    case Laravel = 'Laravel';
    case Javascript = 'Javascript';
    case NodeJS = 'NodeJS';
    case Java = 'Java';
    case CSharp = 'C#';
    case Go = 'Go';
    case Rust = 'Rust';

    /**
     * Get all predefined code builders as an associative array.
     *
     * The array contains lowercase keys corresponding to the code builder's
     * string value and their respective display titles as values.
     *
     * @return array Associative array of code builders.
     */
    public static function toArray(): array
    {
        return [
            self::cURL->value => 'cURL',
            self::PHP->value => 'PHP',
            self::Laravel->value => 'Laravel',
            self::Javascript->value => 'Javascript',
            self::NodeJS->value => 'NodeJS',
            self::Java->value => 'Java',
            self::CSharp->value => 'C#',
            self::Go->value => 'Go',
            self::Rust->value => 'Rust',
        ];
    }
}