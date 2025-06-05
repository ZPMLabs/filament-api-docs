<?php

namespace ZPMLabs\FilamentApiDocsBuilder\CodeBuilders;

/**
 * Abstract class CodeBuilder
 *
 * Provides a base implementation for code builder classes that generate API request examples.
 */
abstract class CodeBuilder implements CodeBuilderContract
{
    /**
     * Constructor to initialize the CodeBuilder instance.
     *
     * @param string $requestType The HTTP request type (e.g., GET, POST).
     * @param bool $authRequired Indicates if authentication is required for the request.
     * @param string $endpoint The API endpoint URL.
     * @param array $headers An array of headers for the request.
     * @param array $queryParams An array of query parameters for the request.
     * @param array $bodyParams An array of body parameters for the request.
     */
    public function __construct(
        protected string $requestType,
        protected bool $authRequired,
        protected string $endpoint,
        protected array $headers,
        protected array $queryParams,
        protected array $bodyParams
    ) {}

    /**
     * Factory method to create a new instance and handle the code generation.
     *
     * @param string $requestType The HTTP request type (e.g., GET, POST).
     * @param bool $authRequired Indicates if authentication is required for the request.
     * @param string $endpoint The API endpoint URL.
     * @param array $headers An array of headers for the request.
     * @param array $queryParams An array of query parameters for the request.
     * @param array $bodyParams An array of body parameters for the request.
     * @return array The generated code example.
     */
    public static function make(
        string $requestType,
        bool $authRequired,
        string $endpoint,
        array $headers,
        array $queryParams,
        array $bodyParams
    ) {
        return (new static($requestType, $authRequired, $endpoint, $headers, $queryParams, $bodyParams))->handle();
    }

    /**
     * Helper function to add indentation to a JSON string.
     *
     * @param string $json The JSON string to indent.
     * @param int $spaces The number of spaces for indentation.
     * @return string The indented JSON string.
     */
    protected function indentJson(string $json, int $spaces = 8): string
    {
        $indent = str_repeat(' ', $spaces);
        $lines = explode("\n", $json);
        return implode("\n$indent", $lines);
    }
}