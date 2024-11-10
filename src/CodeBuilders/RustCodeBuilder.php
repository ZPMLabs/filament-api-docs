<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class RustCodeBuilder
 *
 * Generates Rust code examples for API requests using the `reqwest` library.
 */
class RustCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the Rust API request code example.
     *
     * @return array An array containing the code style and generated Rust code.
     */
    public function handle(): array
    {
        // Initialize Rust code with imports and async main function setup
        $rustCode = "// Rust HTTP request using reqwest\n\n";
        $rustCode .= "use reqwest::Client;\n";
        $rustCode .= "use serde_json::json;\n";
        $rustCode .= "use std::error::Error;\n\n";

        $rustCode .= "#[tokio::main]\n";
        $rustCode .= "async fn main() -> Result<(), Box<dyn Error>> {\n";
        $rustCode .= "    let client = Client::new();\n";

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
        $rustCode .= "    let url = \"" . $this->endpoint . "\";\n";

        // Start building the request
        $rustCode .= "    let request = client." . strtolower($this->requestType) . "(url)\n";

        // Add Authorization header if required
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => "Bearer YOUR_API_TOKEN" // Replace with actual token
            ];
        }

        // Add headers to the request
        if (!empty($this->headers)) {
            foreach ($this->headers as $header) {
                $rustCode .= "        .header(\"" . $header['name'] . "\", \"" . $header['value'] . "\")\n";
            }
        }

        // Add body data for POST, PUT, PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $rustCode .= "        .json(&" . $this->indentJson($bodyData, 8) . ")\n";
        }

        // Complete the request and handle the response
        $rustCode .= "        .send()\n";
        $rustCode .= "        .await?;\n\n";
        $rustCode .= "    let response_text = request.text().await?;\n";
        $rustCode .= "    println!(\"{}\", response_text);\n\n";
        $rustCode .= "    Ok(())\n";
        $rustCode .= "}\n";

        // Return the generated Rust code along with the style
        return [
            'style' => CodeStyle::PHP->value, // Adjust the style if required
            'code' => $rustCode,
        ];
    }
}