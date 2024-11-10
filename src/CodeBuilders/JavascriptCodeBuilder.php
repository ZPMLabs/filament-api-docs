<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class JavascriptCodeBuilder
 *
 * Generates JavaScript code examples for API requests using the `fetch` API.
 */
class JavascriptCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the JavaScript API request code example.
     *
     * @return array An array containing the code style and generated JavaScript code.
     */
    public function handle(): array
    {
        // Initialize JavaScript code with the fetch API
        $jsCode = "// JavaScript fetch request\n\n";

        // Construct the query string for required query parameters
        $queryString = '';
        if (!empty($this->queryParams)) {
            $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
            if (!empty($requiredQueryParams)) {
                $queryString = '?' . http_build_query(array_column($requiredQueryParams, 'value', 'name'));
                $this->endpoint .= $queryString;
            }
        }

        // Define the endpoint and request options
        $jsCode .= "const endpoint = '$this->endpoint';\n";
        $jsCode .= "const options = {\n";
        $jsCode .= "    method: '" . strtoupper($this->requestType) . "',\n";

        // Add Authorization header if required
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => 'Bearer ${API_TOKEN}' // Assuming `API_TOKEN` is defined in the JS context
            ];
        }

        // Construct headers
        if (!empty($this->headers)) {
            $jsCode .= "    headers: {\n";
            foreach ($this->headers as $header) {
                $jsCode .= "        \"" . $header['name'] . "\": \"" . $header['value'] . "\",\n";
            }
            $jsCode = rtrim($jsCode, ",\n") . "\n"; // Remove trailing comma
            $jsCode .= "    },\n";
        }

        // Add body parameters for POST, PUT, PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $jsCode .= "    body: " . $this->indentJson($bodyData, 4) . ",\n";
        }

        $jsCode .= "};\n\n";

        // Add the fetch call
        $jsCode .= "fetch(endpoint, options)\n";
        $jsCode .= "    .then(response => response.json())\n";
        $jsCode .= "    .then(data => console.log(data))\n";
        $jsCode .= "    .catch(error => console.error('Error:', error));\n";

        // Return the generated JavaScript code along with the style
        return [
            'style' => CodeStyle::JAVASCRIPT->value,
            'code' => $jsCode,
        ];
    }
}