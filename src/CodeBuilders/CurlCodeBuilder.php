<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class CurlCodeBuilder
 *
 * Generates cURL command-line examples for API requests.
 */
class CurlCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the cURL API request example.
     *
     * @return array An array containing the code style and generated cURL command.
     */
    public function handle(): array
    {
        // Start with the curl command and the specified HTTP method
        $curl = "curl -X " . strtoupper($this->requestType) . " \"" . $this->endpoint;

        // Append required query parameters to the endpoint URL
        $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
        if (!empty($requiredQueryParams)) {
            $queryString = http_build_query(array_column($requiredQueryParams, 'value', 'name'));
            $curl .= '?' . $queryString;
        }
        $curl .= "\" \\\n";

        // Add Authorization header if required and not already present in headers
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => 'Bearer $API_TOKEN' // Replace with the actual token
            ];
        }

        // Append headers to the cURL command with proper indentation
        foreach ($this->headers as $header) {
            $headerName = $header['name'];
            $headerValue = $header['value'];
            $curl .= "    -H \"$headerName: $headerValue\" \\\n";
        }

        // Append body parameters as JSON if required by the request method
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $curl .= "    -d `" . $this->indentJson($bodyData, 8) . "`";
        } else {
            // Remove the trailing backslash if no body is present
            $curl = rtrim($curl, " \\\n");
        }

        // Return the generated cURL command along with the code style
        return [
            'style' => CodeStyle::GDSCRIPT->value, // Adjust the style value if needed
            'code' => $curl,
        ];
    }
}