<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Actions;

use Illuminate\Support\Collection;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\CSharpCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\CurlCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\GoCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\JavaCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\JavascriptCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\LaravelCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\NodeJsCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\PHPCodeBuilder;
use ZPMLabs\FilamentApiDocsBuilder\CodeBuilders\RustCodeBuilder;

class PredefineCodeBuilderAction
{
    /**
     * Handles the generation of predefined code examples based on the request data.
     *
     * @param array $data Input data containing request details and parameters.
     * @return array An array of code examples for the selected languages.
     */
    public static function handle(array $data)
    {
        // Retrieve available code builders
        $codeBuilders = static::codeExamples();

        // Extract request details
        $requestType = $data['details']['request_type'] ?? 'GET';
        $authRequired = $data['details']['auth_required'] ?? false;
        $endpoint = $data['details']['endpoint'] ?? '$API_ENDPOINT';
        $params = $data['instructions']['params'];

        // Filter query parameters based on visibility conditions
        $queryParams = array_filter($params, function ($param) use ($params) {
            $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
            $conditionally = ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));

            return $param['param_location'] === "query" && $conditionally;
        });

        // Filter header parameters based on visibility conditions
        $headers = array_filter($params, function ($param) use ($params) {
            $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
            $conditionally = ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));

            return $param['param_location'] === "header" && $conditionally;
        });

        // Filter body parameters based on visibility conditions
        $bodyParams = array_filter($params, function ($param) use ($params) {
            $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
            $conditionally = ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));

            return $param['param_location'] === "body" && $conditionally;
        });

        // Get the list of predefined code builders to use
        $predefinedCodeBuilders = $data['request_code']['use_predefined_codes'];

        // Return an empty array if no predefined builders are selected
        if (empty($predefinedCodeBuilders)) {
            return [];
        }

        $codeExamples = [];

        // Generate code examples for each selected language
        foreach ($codeBuilders as $lang => $builderClass) {
            if (in_array($lang, $predefinedCodeBuilders)) {
                $codeExamples[$lang] = $builderClass::make($requestType, $authRequired, $endpoint, $headers, $queryParams, $bodyParams);
            }
        }

        return $codeExamples;
    }

    /**
     * Retrieves the list of available code builders.
     *
     * @return array An associative array of language names and their corresponding builder classes.
     */
    public static function codeExamples()
    {
        // Default code builders
        $codeBuilders = [
            'cURL' => CurlCodeBuilder::class,
            'PHP' => PHPCodeBuilder::class,
            'Laravel' => LaravelCodeBuilder::class,
            'Javascript' => JavascriptCodeBuilder::class,
            'NodeJS' => NodeJsCodeBuilder::class,
            'Java' => JavaCodeBuilder::class,
            'C#' => CSharpCodeBuilder::class,
            'Go' => GoCodeBuilder::class,
            'Rust' => RustCodeBuilder::class,
        ];

        // Merge with additional builders from configuration
        $codeBuilders = array_merge($codeBuilders, config('filament-api-docs.code_builders', []));

        return $codeBuilders;
    }

    /**
     * Converts the available code builders into a simple array format.
     *
     * @return Collection An array mapping language names to themselves.
     */
    public static function toArray()
    {
        // Map language names to themselves for dropdown or selection purposes
        return collect(static::codeExamples())->mapWithKeys(fn($value, $key) => [$key => $key]);
    }
}
