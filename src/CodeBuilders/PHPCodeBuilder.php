<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class PHPCodeBuilder
 *
 * Generates PHP code examples for API requests using GuzzleHttp.
 */
class PHPCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the PHP API request code example.
     *
     * @return array An array containing the code style and generated PHP code.
     */
    public function handle(): array
    {
        // Start building the PHP code with imports and client initialization
        $phpCode = "<?php\n\n";
        $phpCode .= "use GuzzleHttp\\Client;\n\n";
        $phpCode .= "\$client = new Client();\n\n";

        // Add query parameters to the URL if required
        $queryString = '';
        $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
        if (!empty($requiredQueryParams)) {
            $queryString = '?' . http_build_query(array_column($requiredQueryParams, 'value', 'name'));
            $this->endpoint .= $queryString;
        }

        // Start building the request with the HTTP method and endpoint
        $phpCode .= "\$response = \$client->request('" . strtoupper($this->requestType) . "', '" . $this->endpoint . "', [\n";

        // Add Authorization header if required
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => 'Bearer $API_TOKEN' // Replace with actual token
            ];
        }

        // Include headers if present
        if (!empty($this->headers)) {
            $phpCode .= "    'headers' => [\n";
            foreach ($this->headers as $header) {
                $phpCode .= "        '" . $header['name'] . "' => '" . $header['value'] . "',\n";
            }
            $phpCode .= "    ],\n";
        }

        // Include body data for POST, PUT, PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $indentedBodyData = $this->indentJson($bodyData, 4);
            $phpCode .= "    'body' => " . 
                str($indentedBodyData)
                    ->replace('{', '[')
                    ->replace('}', ']')
                    ->replace(':', ' =>')
            .",\n";
        }

        // Close the request array and output the response body
        $phpCode .= "]);\n\n";
        $phpCode .= "echo \$response->getBody();\n";

        // Return the generated PHP code along with the style
        return [
            'style' => CodeStyle::PHP->value,
            'code' => $phpCode,
        ];
    }
}