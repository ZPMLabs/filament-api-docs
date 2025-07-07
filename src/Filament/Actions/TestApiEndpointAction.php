<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use ZPMLabs\FilamentApiDocsBuilder\Enums\HttpStatuses;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Tempest\Highlight\Highlighter;
use Tempest\Highlight\Themes\InlineTheme;

/**
 * Class TestApiEndpointAction
 *
 * Provides functionality to test API endpoints dynamically using a form-based modal.
 * It supports token-based authentication, handles request parameters,
 * and displays a formatted JSON response with status metadata.
 */
class TestApiEndpointAction extends Action
{
    /**
     * Configure the action for testing API endpoints.
     *
     * @param array $item API details (e.g., endpoint, request type, etc.).
     * @param array $parameters Specific parameters for the request.
     * @param Collection $allParams All parameters for the current API context.
     * @return $this
     */
    public function item($item, $parameters, $allParams)
    {
        // Build form components dynamically based on parameters and authorization requirements
        $testingForm = [];

        if ($item['details']['auth_required']) {
            $testingForm[] = TextInput::make('token')
                ->columnSpanFull()
                ->revealable()
                ->password(); // Token input for authentication
        }

        $testingForm = [...$testingForm, ...$this->buildTestingFormComponents($parameters, $allParams)];

        // Configure the action modal
        $this->hidden(fn() => empty($item['details']['endpoint']))
            ->label('Test Endpoint')
            ->modalDescription($item['details']['endpoint'])
            ->modalWidth(Width::FiveExtraLarge)
            ->modalCancelAction(false)
            ->modalSubmitAction(false)
            ->form([
                Grid::make()
                    ->columns()
                    ->schema([
                        Grid::make()
                            ->columnSpan(1)
                            ->schema($testingForm), // Form fields for API testing
                        ViewField::make('response')
                            ->view('filament-api-docs-builder::filament.viewJson')
                            ->default(['response' => '{}'])
                            ->columnSpan(1) // Display formatted response
                    ]),
                Actions::make([
                    Action::make('send')
                        ->action(function (Set $set, Get $get) use ($item, $allParams) {
                            // Handle request sending and response processing
                            $data = $get();
                            $endpoint = $item['details']['endpoint'];

                            $this->handleResponse(
                                $this->sendRequest(
                                    $item,
                                    $this->handleData($data, $allParams, $endpoint),
                                    $endpoint
                                ),
                                $set
                            );
                        })
                ])
            ]);

        return $this;
    }

    /**
     * Handles the API response and updates the UI with formatted data.
     *
     * @param \Illuminate\Http\Client\Response $response API response.
     * @param Set $set State setter for the form.
     */
    protected function handleResponse($response, $set)
    {
        $responseData = json_decode($response->body(), true);
        $prettyResponse = json_encode($responseData, JSON_PRETTY_PRINT);

        // Highlight the JSON response using a syntax highlighter
        $formated = (new Highlighter(new InlineTheme(__DIR__ . '/../../../resources/themes/solarized-dark.css')))
            ->parse($prettyResponse, 'json');

        $set('response', [
            'response' => $formated,
            'status' => $response->status(),
            'color' => match (true) {
                $response->status() >= 100 && $response->status() < 200 => 'info',       // Informational
                $response->status() >= 200 && $response->status() < 300 => 'success',    // Success
                $response->status() >= 300 && $response->status() < 400 => 'prima',      // Redirection
                $response->status() >= 400 && $response->status() < 500 => 'warning',    // Client errors
                $response->status() >= 500 && $response->status() < 600 => 'danger',     // Server errors
                default => 'default',
            },
            'icon' => HttpStatuses::from($response->status())->getIcon(),
            'text' => HttpStatuses::from($response->status())->getLabel()
        ]);
    }

    /**
     * Sends the API request based on its type and data.
     *
     * @param array $item API details.
     * @param array $data Request data.
     * @param string $endpoint API endpoint URL.
     * @return \Illuminate\Http\Client\Response API response.
     */
    protected function sendRequest($item, $data, &$endpoint)
    {
        $token = $data['token'];
        $authRequired = $item['details']['auth_required'];
        $requestType = strtolower($item['details']['request_type']);

        switch (strtoupper($requestType)) {
            case 'GET':
            case 'DELETE':
            case 'OPTIONS':
            case 'HEAD':
                $response = $authRequired
                    ? Http::withHeaders($data['header'])->withToken($token)->withQueryParameters($data['query'])->$requestType($endpoint)
                    : Http::$requestType($endpoint)->withHeaders($data['header'])->withQueryParameters($data['query']);
                break;

            case 'POST':
            case 'PUT':
            case 'PATCH':
                $response = $authRequired
                    ? Http::withHeaders($data['header'])->withToken($token)->withQueryParameters($data['query'])->$requestType($endpoint, $data['body'])
                    : Http::$requestType($endpoint, $data['body'])->withHeaders($data['header'])->withQueryParameters($data['query']);
                break;

            default:
                throw new InvalidArgumentException("Unsupported request type: $requestType");
        }

        return $response;
    }

    /**
     * Processes and transforms data for the API request.
     *
     * @param array $data Form data.
     * @param Collection $allParams All parameters in the API context.
     * @param string $endpoint API endpoint URL.
     * @return array Processed data.
     */
    protected function handleData($data, $allParams, &$endpoint)
    {
        $locations = ['header', 'route', 'body', 'query'];

        foreach ($locations as $location) {
            $data[$location] = $data[$location] ?? [];

            foreach ($data[$location] as $key => &$value) {
                $cleanKey = str($key)->replace('*', '')->toString();

                $param = $allParams->where('name', $cleanKey)->first();
                if ($param) {
                    if ($param['param_type'] === 'boolean') {
                        $value = $value === 'false' || $value === '0' ? false : true;
                    } elseif ($param['param_type'] === 'number') {
                        $value = (float) $value;
                    }
                }

                if ($location === 'route') {
                    $endpoint = str($endpoint)->replace($cleanKey, $value)->replace('{', '')->replace('}', '');
                } else {
                    if ($cleanKey !== $key) {
                        $data[$location][$cleanKey] = $value;
                        unset($data[$location][$key]);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Builds dynamic form components for testing based on parameters.
     *
     * @param array $parameters Parameters grouped by location.
     * @param Collection $allParams All parameters in the API context.
     * @return array Form components.
     */
    protected function buildTestingFormComponents($parameters, $allParams)
    {
        $components = [];

        foreach ($parameters as $location => $data) {
            if (!empty($data['params'])) {
                $components[$location] = KeyValue::make(strtolower($location))
                    ->label(ucfirst($location))
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->hint('Fields with * are required!')
                    ->default(collect($data['params'])->mapWithKeys(fn($item) => [$item['required'] ? $item['name'] . '*' : $item['name'] => $item['value']])->toArray())
                    ->editableKeys(false)
                    ->afterStateUpdated(fn($state, Set $set) => $set($location, $this->updateStateOnChange($state, $allParams, $location)))
                    ->live();
            }
        }

        return $components;
    }

    /**
     * Updates form state based on conditional visibility rules.
     *
     * @param array $state Current form state.
     * @param Collection $allParams All parameters in the API context.
     * @param string $location Parameter location.
     * @return array Updated state.
     */
    protected function updateStateOnChange($state, $allParams, $location)
    {
        foreach ($allParams->where('visible', 'conditionally') as $param) {
            $name = $param['required'] ? $param['name'] . '*' : $param['name'];
            $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : $allParams->where('name', $param['visibility_condition_param'])->first();

            if ($this->shouldRemoveParamFromState($param, $location, $state, $name, $condParam)) {
                unset($state[$name]);
            }
        }

        // TODO: these do not work properly, fix them

        foreach ($allParams->where('visible', 'conditionally') as $param) {
            $name = $param['required'] ? $param['name'] . '*' : $param['name'];
            $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : $allParams->where('name', $param['visibility_condition_param'])->first();

            if ($this->shouldAddParamToState($param, $location, $state, $name, $condParam)) {
                $state[$name] = $param['value'];
            }
        }

        return $state;
    }

    /**
     * Determines if a parameter should be added to the state.
     */
    protected function shouldAddParamToState($param, $location, $state, $name, $condParam): bool
    {
        $condParamName = $condParam['required'] ? $condParam['name'] . '*' : $condParam['name'];

        return $param['param_location'] === $location &&
            !array_key_exists($name, $state) &&
            array_key_exists($condParamName, $state) &&
            $state[$condParamName] === $param['visibility_condition_value'];
    }

    /**
     * Determines if a parameter should be removed from the state.
     */
    protected function shouldRemoveParamFromState($param, $location, $state, $name, $condParam): bool
    {
        $condParamName = $condParam['required'] ? $condParam['name'] . '*' : $condParam['name'];

        return $param['param_location'] === $location &&
            array_key_exists($name, $state) &&
            array_key_exists($condParamName, $state) &&
            $state[$condParamName] !== $param['visibility_condition_value'];
    }
}