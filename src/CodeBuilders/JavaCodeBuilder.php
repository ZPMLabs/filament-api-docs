<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class JavaCodeBuilder
 *
 * Generates Java code examples for API requests using the `HttpClient` class.
 */
class JavaCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the Java API request code example.
     *
     * @return array An array containing the code style and generated Java code.
     */
    public function handle(): array
    {
        // Start with Java imports and class declaration
        $javaCode = "// Java HTTP request using HttpClient\n\n";
        $javaCode .= "import java.net.URI;\n";
        $javaCode .= "import java.net.http.HttpClient;\n";
        $javaCode .= "import java.net.http.HttpRequest;\n";
        $javaCode .= "import java.net.http.HttpResponse;\n";
        $javaCode .= "import java.net.http.HttpHeaders;\n";
        $javaCode .= "import java.net.http.HttpRequest.BodyPublishers;\n";
        $javaCode .= "import java.net.http.HttpResponse.BodyHandlers;\n";
        $javaCode .= "import java.util.Map;\n\n";

        $javaCode .= "public class ApiRequest {\n\n";
        $javaCode .= "    public static void main(String[] args) throws Exception {\n";
        $javaCode .= "        HttpClient client = HttpClient.newHttpClient();\n";

        // Add query parameters to the endpoint URL if required
        $queryString = '';
        if (!empty($this->queryParams)) {
            $requiredQueryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
            if (!empty($requiredQueryParams)) {
                $queryString = '?' . http_build_query(array_column($requiredQueryParams, 'value', 'name'));
                $this->endpoint .= $queryString;
            }
        }

        // Define the API endpoint
        $javaCode .= "        String endpoint = \"" . $this->endpoint . "\";\n";

        // Start building the HTTP request
        $javaCode .= "        HttpRequest.Builder requestBuilder = HttpRequest.newBuilder()\n";
        $javaCode .= "            .uri(URI.create(endpoint))\n";
        $javaCode .= "            .method(\"" . strtoupper($this->requestType) . "\", ";

        // Add body data for POST, PUT, PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $javaCode .= "BodyPublishers.ofString(\"" . $this->indentJson($bodyData, 12) . "\")";
        } else {
            $javaCode .= "BodyPublishers.noBody()";
        }

        $javaCode .= ");\n\n";

        // Add Authorization header if required and not already in headers
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => 'Bearer YOUR_API_TOKEN' // Replace with actual token
            ];
        }

        // Append headers to the request
        foreach ($this->headers as $header) {
            $javaCode .= "        requestBuilder.header(\"" . $header['name'] . "\", \"" . $header['value'] . "\");\n";
        }

        // Build and send the HTTP request
        $javaCode .= "\n";
        $javaCode .= "        HttpRequest request = requestBuilder.build();\n\n";
        $javaCode .= "        HttpResponse<String> response = client.send(request, BodyHandlers.ofString());\n";
        $javaCode .= "        System.out.println(response.body());\n";
        $javaCode .= "    }\n";
        $javaCode .= "}\n";

        // Return the generated Java code along with the style
        return [
            'style' => CodeStyle::PHP->value, // Adjust the style if necessary
            'code' => $javaCode,
        ];
    }
}