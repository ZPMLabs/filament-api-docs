<?php

namespace ZPMLabs\FilamentApiDocsBuilder\CodeBuilders;

use ZPMLabs\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class LaravelCodeBuilder
 *
 * Generates Laravel HTTP client code examples for API requests using the `Http` facade.
 */
class LaravelCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the Laravel API request code example.
     *
     * @return array An array containing the code style and generated Laravel PHP code.
     */
    public function handle(): array
    {
        // Start building the PHP code with necessary imports
        $phpCode = "<?php\n\n";
        $phpCode .= "use Illuminate\\Support\\Facades\\Http;\n\n";

        // Add query parameters to the endpoint URL if required
        if (!empty($this->queryParams)) {
            $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
            if (!empty($requiredQueryParams)) {
                $queryString = http_build_query(array_column($requiredQueryParams, 'value', 'name'));
                $this->endpoint .= '?' . $queryString;
            }
        }

        // Start building the request
        $phpCode .= "\$response = Http::";
        if ($this->authRequired) {
            $phpCode .= "withToken(\$apiToken)"; // Assumes `$apiToken` is defined elsewhere for authentication
        }

        // Add headers if any
        if (!empty($this->headers)) {
            $headers = json_encode(array_column($this->headers, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $indentedHeaders = $this->indentJson($headers, 4);
            $phpCode .= "\n    ->withHeaders(" . 
                str($indentedHeaders)
                    ->replace('{', '[')
                    ->replace('}', ']')
                    ->replace(':', ' =>')
            . ")";
        }

        // Add the request type and endpoint
        $phpCode .= "\n    ->" . strtolower($this->requestType) . "('" . $this->endpoint . "'";

        // Include body data if the request method supports it and body parameters are provided
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $indentedBodyData = $this->indentJson($bodyData, 8);
            $phpCode .= ",\n        " . 
                str($indentedBodyData)
                    ->replace('{', '[')
                    ->replace('}', ']')
                    ->replace(':', ' =>')
            . "\n    ";
        }

        $phpCode .= ");\n\n";
        $phpCode .= "// Output the response body\n";
        $phpCode .= "echo \$response->body();\n";

        // Return the generated PHP code along with the style
        return [
            'style' => CodeStyle::PHP->value,
            'code' => $phpCode,
        ];
    }
}