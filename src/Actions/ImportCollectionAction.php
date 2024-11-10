<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Actions;

use Illuminate\Support\Facades\File;

class ImportCollectionAction
{
    /**
     * Execute the import action and convert JSON to custom format.
     *
     * @param string $filePath Path to JSON file.
     * @return array Formatted array in the custom structure.
     */
    public function execute(string $filePath): array
    {
        // Decode JSON file content
        $postmanData = json_decode(File::get($filePath), true);

        // Initialize the formatted collection with title, description, and version
        $formattedData = [
            'title' => ($postmanData['info']['name'] ?? 'Imported Collection'),
            'description' => $postmanData['info']['description'],
            'version' => 1,
            'data' => []
        ];

        // Process each request item in the Postman collection
        foreach ($postmanData['item'] as $item) {
            $formattedData['data'][] = $this->formatRequest($item);
        }

        return $formattedData;
    }

    /**
     * Format a single Postman request item into the custom structure.
     *
     * @param array $item Postman request item.
     * @return array Formatted request array in custom structure.
     */
    protected function formatRequest(array $item): array
    {
        return [
            'details' => $this->formatDetails($item),
            'instructions' => $this->formatInstructions($item),
            'request_code' => $this->formatRequestCode(),
            'response' => $this->formatResponse($item),
        ];
    }

    /**
     * Format the details section of a request.
     *
     * @param array $item Request item from the Postman collection.
     * @return array Formatted details.
     */
    protected function formatDetails(array $item): array
    {
        return [
            'title' => $item['name'],
            'endpoint' => $item['request']['url']['raw'] ?? null,
            'request_type' => strtoupper($item['request']['method']),
            'auth_required' => $this->checkAuthRequired($item),
            'description' => $item['request']['description'] ?? null,
            'collapsed' => true, // Default to a collapsed state.
        ];
    }

    /**
     * Check if the Authorization header is required in the request.
     *
     * @param array $item Request item from the Postman collection.
     * @return bool True if Authorization is required, otherwise false.
     */
    protected function checkAuthRequired(array $item): bool
    {
        // Check if the request headers include an Authorization header
        return collect($item['request']['header'] ?? [])
            ->contains(fn($header) => strtolower($header['key']) === 'authorization');
    }

    /**
     * Format the instructions section of a request.
     *
     * @param array $item Request item from the Postman collection.
     * @return array Formatted instructions including parameters.
     */
    protected function formatInstructions(array $item): array
    {
        $params = [];

        // Parse query parameters
        if (isset($item['request']['url']['query'])) {
            foreach ($item['request']['url']['query'] as $queryParam) {
                $params[] = [
                    'param_type' => 'query',
                    'name' => $queryParam['key'],
                    'value' => $queryParam['value'] ?? '',
                    'description' => $queryParam['description'] ?? '',
                    'required' => !empty($queryParam['required']),
                    'param_location' => 'query',
                    'visible' => 'always',
                    'visibility_condition_param' => null,
                    'visibility_condition_value' => null,
                ];
            }
        }

        // Parse header parameters
        foreach ($item['request']['header'] as $header) {
            $params[] = [
                'param_type' => 'header',
                'name' => $header['key'],
                'value' => $header['value'],
                'description' => $header['description'] ?? '',
                'required' => true,
                'param_location' => 'header',
                'visible' => 'always',
                'visibility_condition_param' => null,
                'visibility_condition_value' => null,
            ];
        }

        // Parse body parameters
        $bodyParams = $this->formatBodyParams($item);
        $params = array_merge($params, $bodyParams);

        return [
            'description' => $item['request']['description'] ?? '',
            'params' => $params,
        ];
    }

    /**
     * Format request code settings.
     *
     * @return array Formatted request code settings.
     */
    protected function formatRequestCode(): array
    {
        return [
            'use_predefined_codes' => config('filament-api-docs-builder.importer.predefined_codes'),
            'use_custom_codes' => false,
            'custom_code' => [],
        ];
    }

    /**
     * Format the response section of a request.
     *
     * @param array $item Request item from the Postman collection.
     * @return array Formatted responses.
     */
    protected function formatResponse(array $item): array
    {
        $responses = [];

        // Process each response in the request
        foreach ($item['response'] ?? [] as $response) {
            $responses[] = [
                'status' => (string) ($response['code'] ?? ''),
                'title' => $response['name'] ?? '',
                'description' => $response['description'] ?? '',
                'icon' => $this->getResponseIcon($response['code'] ?? 200),
                'color' => $this->getResponseColor($response['code'] ?? 200),
                'body' => json_encode($response['body'] ?? [], JSON_PRETTY_PRINT),
            ];
        }

        return $responses;
    }

    /**
     * Format body parameters.
     *
     * @param array $item Request item from the Postman collection.
     * @param bool $rawOnly If true, return JSON string; otherwise, an array of parameters.
     * @return array|string Formatted body parameters.
     */
    protected function formatBodyParams(array $item, $rawOnly = false)
    {
        if (!isset($item['request']['body']['raw'])) {
            return $rawOnly ? '' : [];
        }

        $bodyParams = json_decode($item['request']['body']['raw'], true);

        if ($rawOnly) {
            return json_encode($bodyParams, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        // Map body parameters into a structured array
        return collect($bodyParams)->map(function ($value, $key) {
            return [
                'param_type' => 'body',
                'name' => $key,
                'value' => $value,
                'description' => '',
                'required' => true,
                'param_location' => 'body',
                'visible' => 'always',
                'visibility_condition_param' => null,
                'visibility_condition_value' => null,
            ];
        })->values()->toArray();
    }

    /**
     * Get the icon for a response based on its status code.
     *
     * @param int $status HTTP status code.
     * @return string Corresponding icon name.
     */
    protected function getResponseIcon(int $status): string
    {
        return $status >= 200 && $status < 300 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle';
    }

    /**
     * Get the color for a response based on its status code.
     *
     * @param int $status HTTP status code.
     * @return string Corresponding color name.
     */
    protected function getResponseColor(int $status): string
    {
        return $status >= 200 && $status < 300 ? 'teal' : 'red';
    }
}
