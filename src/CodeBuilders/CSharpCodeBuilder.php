<?php

namespace InfinityXTech\FilamentApiDocsBuilder\CodeBuilders;

use InfinityXTech\FilamentApiDocsBuilder\Enums\CodeStyle;

/**
 * Class CSharpCodeBuilder
 *
 * Generates C# code examples for API requests using the HttpClient class.
 */
class CSharpCodeBuilder extends CodeBuilder
{
    /**
     * Handles the generation of the C# API request code example.
     *
     * @return array An array containing the code style and generated C# code.
     */
    public function handle(): array
    {
        // Initialize the C# code with necessary imports and boilerplate
        $csharpCode = "// C# HTTP request using HttpClient\n\n";
        $csharpCode .= "using System;\n";
        $csharpCode .= "using System.Net.Http;\n";
        $csharpCode .= "using System.Net.Http.Headers;\n";
        $csharpCode .= "using System.Threading.Tasks;\n\n";

        $csharpCode .= "public class ApiRequest {\n\n";
        $csharpCode .= "    public static async Task Main(string[] args) {\n";
        $csharpCode .= "        using (var client = new HttpClient()) {\n";

        // Construct the query string for required query parameters
        $queryString = '';
        $queryParams = array_filter($this->queryParams, fn($param) => $param['required'] === true);
        if (!empty($queryParams)) {
            $queryString = '?' . http_build_query(array_column($queryParams, 'value', 'name'));
            $this->endpoint .= $queryString;
        }

        // Add the endpoint to the C# code
        $csharpCode .= "            var endpoint = \"" . $this->endpoint . "\";\n";

        // Add Authorization header if required and not already present
        if ($this->authRequired && !array_search('Authorization', array_column($this->headers, 'name'))) {
            $this->headers[] = [
                'name' => 'Authorization',
                'value' => "Bearer YOUR_API_TOKEN" // Replace with an actual token
            ];
        }

        // Add headers to the HttpClient instance
        foreach ($this->headers as $header) {
            $csharpCode .= "            client.DefaultRequestHeaders.Add(\"" . $header['name'] . "\", \"" . $header['value'] . "\");\n";
        }

        // Handle body content for POST, PUT, and PATCH requests
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $bodyData = json_encode(array_column($this->bodyParams, 'value', 'name'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $indentedBodyData = $this->indentJson($bodyData, 16); // Properly indent the JSON body
            $csharpCode .= "            var content = new StringContent(\n";
            $csharpCode .= "                @\"" . $indentedBodyData . "\",\n";
            $csharpCode .= "                System.Text.Encoding.UTF8,\n";
            $csharpCode .= "                \"application/json\"\n";
            $csharpCode .= "            );\n";
        } else {
            $csharpCode .= "            var content = null;\n";
        }

        // Generate the request execution code
        $csharpCode .= "            var response = await client." . ucfirst(strtolower($this->requestType)) . "Async(endpoint";
        if (in_array(strtoupper($this->requestType), ['POST', 'PUT', 'PATCH']) && !empty($this->bodyParams)) {
            $csharpCode .= ", content";
        }
        $csharpCode .= ");\n";

        // Read and output the response
        $csharpCode .= "            var responseBody = await response.Content.ReadAsStringAsync();\n";
        $csharpCode .= "            Console.WriteLine(responseBody);\n";
        $csharpCode .= "        }\n";
        $csharpCode .= "    }\n";
        $csharpCode .= "}\n";

        // Return the generated code along with the style
        return [
            'style' => CodeStyle::PHP->value,
            'code' => $csharpCode,
        ];
    }
}