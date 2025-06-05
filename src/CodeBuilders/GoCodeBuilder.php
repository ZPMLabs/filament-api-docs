<?php

namespace ZPMLabs\FilamentApiDocsBuilder\CodeBuilders;

use ZPMLabs\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class GoCodeBuilder
 *
 * Generates Go code examples for API requests using the `net/http` package.
 */
class GoCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the Go API request code example.
     *
     * @return array An array containing the code style and generated Go code.
     */
    public function handle(): array
    {
        // Initialize Go code with package and imports
        $goCode = "// Go HTTP request using net/http\n\n";
        $goCode .= "package main\n\n";
        $goCode .= "import (\n";
        $goCode .= "    \"bytes\"\n";
        $goCode .= "    \"encoding/json\"\n";
        $goCode .= "    \"fmt\"\n";
        $goCode .= "    \"io/ioutil\"\n";
        $goCode .= "    \"net/http\"\n";
        $goCode .= ")\n\n";

        $goCode .= "func main() {\n";

        // Construct the query string for required query parameters
        $queryString = '';
        if (!empty($this->queryParams)) {
            $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
            if (!empty($requiredQueryParams)) {
                $queryString = '?' . http_build_query(array_column($requiredQueryParams, 'value', 'name'));
                $this->endpoint .= $queryString;
            }
        }

        // Define the API URL
        $goCode .= "    url := \"" . $this->endpoint . "\"\n";

        // Add body data for POST, PUT, PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $goCode .= "    requestBody := []byte(`" . $this->indentJson($bodyData, 4) . "`)\n";
        } else {
            $goCode .= "    var requestBody []byte\n";
        }

        // Create the HTTP request
        $goCode .= "    req, err := http.NewRequest(\"" . strtoupper($this->requestType) . "\", url, bytes.NewBuffer(requestBody))\n";
        $goCode .= "    if err != nil {\n";
        $goCode .= "        fmt.Println(\"Error creating request:\", err)\n";
        $goCode .= "        return\n";
        $goCode .= "    }\n\n";

        // Add Authorization header if required and not already present
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => "Bearer YOUR_API_TOKEN" // Replace with actual token
            ];
        }

        // Add headers to the request
        foreach ($this->headers as $header) {
            $goCode .= "    req.Header.Set(\"" . $header['name'] . "\", \"" . $header['value'] . "\")\n";
        }

        // Send the HTTP request and handle the response
        $goCode .= "\n";
        $goCode .= "    client := &http.Client{}\n";
        $goCode .= "    resp, err := client.Do(req)\n";
        $goCode .= "    if err != nil {\n";
        $goCode .= "        fmt.Println(\"Error sending request:\", err)\n";
        $goCode .= "        return\n";
        $goCode .= "    }\n";
        $goCode .= "    defer resp.Body.Close()\n\n";

        // Read and print the response body
        $goCode .= "    body, err := ioutil.ReadAll(resp.Body)\n";
        $goCode .= "    if err != nil {\n";
        $goCode .= "        fmt.Println(\"Error reading response body:\", err)\n";
        $goCode .= "        return\n";
        $goCode .= "    }\n\n";

        $goCode .= "    fmt.Println(string(body))\n";
        $goCode .= "}\n";

        // Return the generated Go code along with the style
        return [
            'style' => CodeStyle::PHP->value, // Adjust if required
            'code' => $goCode,
        ];
    }
}