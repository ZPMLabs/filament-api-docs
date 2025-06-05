<?php

namespace ZPMLabs\FilamentApiDocsBuilder\CodeBuilders;

use ZPMLabs\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class NodeJsCodeBuilder
 *
 * Generates Node.js code examples for API requests using the `axios` library.
 */
class NodeJsCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the Node.js API request code example.
     *
     * @return array An array containing the code style and generated JavaScript code.
     */
    public function handle(): array
    {
        // Initialize JavaScript code with the axios import
        $jsCode = "// Node.js request using axios\n\n";
        $jsCode .= "const axios = require('axios');\n\n";

        // Construct the query string for required query parameters
        $queryString = '';
        if (!empty($this->queryParams)) {
            $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
            if (!empty($requiredQueryParams)) {
                $queryString = '?' . http_build_query(array_column($requiredQueryParams, 'value', 'name'));
                $this->endpoint .= $queryString;
            }
        }

        // Define the API endpoint
        $jsCode .= "const endpoint = '$this->endpoint';\n";

        // Start building the request options
        $jsCode .= "const options = {\n";
        $jsCode .= "    method: '" . strtoupper($this->requestType) . "',\n";
        $jsCode .= "    url: endpoint,\n";

        // Add Authorization header if required
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => 'Bearer ${API_TOKEN}' // Assuming `API_TOKEN` is defined in the Node.js context
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
            $jsCode .= "    data: " . $this->indentJson($bodyData, 4) . ",\n";
        }

        $jsCode .= "};\n\n";

        // Add the axios request
        $jsCode .= "axios(options)\n";
        $jsCode .= "    .then(response => console.log(response.data))\n";
        $jsCode .= "    .catch(error => console.error('Error:', error));\n";

        // Return the generated JavaScript code along with the style
        return [
            'style' => CodeStyle::JAVASCRIPT->value,
            'code' => $jsCode,
        ];
    }
}