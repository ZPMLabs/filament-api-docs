<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

/**
 * Interface CodeBuilderContract
 *
 * Defines the contract for code builder classes that generate API request examples.
 */
interface CodeBuilderContract
{
    /**
     * Handles the generation of the code example.
     *
     * @return array The generated code example as an associative array.
     */
    public function handle(): array;
}