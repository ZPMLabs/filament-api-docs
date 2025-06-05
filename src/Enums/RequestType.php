<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Enums;

/**
 * Enum RequestType
 *
 * Represents the HTTP request methods commonly used in API operations.
 */
enum RequestType: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
    case PATCH = 'patch';
    case OPTIONS = 'options';
    case HEAD = 'head';

    /**
     * Get all request types as an associative array.
     *
     * The array contains lowercase enum values as keys and their uppercase
     * titles as values for display or mapping purposes.
     *
     * @return array Associative array of request types.
     */
    public static function toArray(): array
    {
        return [
            self::GET->value => 'GET',
            self::POST->value => 'POST',
            self::PUT->value => 'PUT',
            self::DELETE->value => 'DELETE',
            self::PATCH->value => 'PATCH',
            self::OPTIONS->value => 'OPTIONS',
            self::HEAD->value => 'HEAD',
        ];
    }
}