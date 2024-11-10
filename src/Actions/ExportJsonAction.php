<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class ExportJsonAction
{
    /**
     * Executes the export action to generate and download a JSON file.
     *
     * @param string $name The name of the collection.
     * @param string $description The description of the collection.
     * @param array $data The data to be exported, including request details.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse A streamed download response for the JSON file.
     */
    public function execute($name, $description, array $data)
    {
        // Prepare the export data structure
        $exportData = [
            'info' => [
                'name' => $name,
                '_postman_id' => (string) Str::uuid(),
                'description' => $description,
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => $this->formatRequests($data),
        ];

        // Convert the export data to a JSON string
        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Return the JSON content as a downloadable file
        return Response::streamDownload(function () use ($jsonContent) {
            echo $jsonContent;
        }, str($name)->replace(' ', '_')->toString() . '_collection.json', [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Formats the request data into a Postman-compatible structure.
     *
     * @param array $data An array of request data.
     * @return array An array of formatted request items.
     */
    protected function formatRequests(array $data)
    {
        // Filter and map request data to the required format
        return collect($data)->filter(fn ($item) => !empty($item['details']['endpoint']))->map(function ($item) {
            return [
                'name' => $item['details']['title'],
                'request' => [
                    'method' => strtoupper($item['details']['request_type']),
                    'header' => $this->formatHeaders($item['instructions']['params'], $item['details']['auth_required']),
                    'url' => [
                        'raw' => $this->formatRouteParams($item['details']['endpoint'], $item['instructions']['params']),
                        'host' => explode('/', parse_url($item['details']['endpoint'], PHP_URL_HOST)),
                        'path' => explode('/', trim(parse_url($item['details']['endpoint'], PHP_URL_PATH), '/')),
                        'query' => $this->formatQueryParams($item['instructions']['params']),
                    ],
                    'body' => $this->formatBody($item['instructions']['params']),
                ],
                'response' => $this->formatResponses($item['response'] ?? []),
            ];
        })->values()->toArray();
    }

    /**
     * Formats headers for a request based on the given parameters and authentication requirements.
     *
     * @param array $params Request parameters.
     * @param bool $authRequired Indicates if an Authorization header is required.
     * @return array An array of formatted headers.
     */
    protected function formatHeaders(array $params, $authRequired = false)
    {
        // Filter parameters to extract headers
        $headers = collect($params)
            ->filter(function($param)use ($params) {
                $condParam = collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
                return $param['param_location'] === 'header' && ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));
            })
            ->map(function ($param) {
                return [
                    'key' => $param['name'],
                    'value' => $param['value'],
                ];
            });

        // Add Authorization header if required and missing
        if ($authRequired && !$headers->contains('key', 'Authorization')) {
            $headers->push([
                'key' => 'Authorization',
                'value' => 'Bearer $API_TOKEN', // Replace with the actual token if available
            ]);
        }

        return $headers->values()->toArray();
    }

    /**
     * Formats route parameters into the endpoint URL.
     *
     * @param string $endpoint The endpoint URL template.
     * @param array $params The parameters to replace in the URL.
     * @return string The formatted endpoint URL.
     */
    protected function formatRouteParams($endpoint, array $params)
    {
        // Replace placeholders in the endpoint URL with parameter values
        collect($params)->filter(function($param) use ($params) {
            $condParam = collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
            return $param['param_location'] === 'query' && ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));
        })
        ->each(function ($param) use (&$endpoint) {
            $endpoint = str($endpoint)->replace($param['name'], $param['value'])->replace('{', '')->replace('}', '');
        });

        return $endpoint;
    }

    /**
     * Formats query parameters for the request.
     *
     * @param array $params The request parameters.
     * @return array An array of formatted query parameters.
     */
    protected function formatQueryParams(array $params)
    {
        // Filter and format query parameters
        return collect($params)
            ->filter(function($param)use ($params) {
                $condParam = collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
                return $param['param_location'] === 'query' && ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));
            })
            ->map(function ($param) {
                return [
                    'key' => $param['name'],
                    'value' => $param['value'],
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Formats the request body for the given parameters.
     *
     * @param array $params The request parameters.
     * @return array A formatted request body.
     */
    protected function formatBody(array $params)
    {
        // Extract and format body parameters
        $bodyParams = collect($params)
            ->filter(function($param)use ($params) {
                $condParam = collect($params)->where('name', '=', $param['visibility_condition_param'])->first();
                return $param['param_location'] === 'body' && ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));
            })
            ->mapWithKeys(function ($param) {
                return [$param['name'] => $param['value']];
            })
            ->toArray();

        return [
            'mode' => 'raw',
            'raw' => json_encode($bodyParams, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];
    }

    /**
     * Formats response examples for the given data.
     *
     * @param array $responses An array of response data.
     * @return array An array of formatted response examples.
     */
    protected function formatResponses(array $responses)
    {
        // Map and format response data
        return collect($responses)->map(function ($response) {
            return [
                'name' => $response['title'],
                'status' => $response['title'],
                'code' => (int) $response['status'],
                'body' => $response['body'],
                'header' => [
                    [
                        'key' => 'Content-Type',
                        'value' => 'application/json',
                    ]
                ],
            ];
        })->toArray();
    }
}
