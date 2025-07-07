<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Infolists;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use ZPMLabs\FilamentApiDocsBuilder\Actions\PredefineCodeBuilderAction;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Actions\TestApiEndpointAction;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Forms\Components\Code;

/**
 * Class ApiDocsInfolistBuilder
 *
 * This class builds an infolist schema for displaying API documentation in a structured, dynamic manner.
 * It organizes API data into sections, parameters, and response examples, with predefined code snippets and tabs.
 */
class ApiDocsInfolistBuilder
{
    /**
     * Creates a new instance and generates the schema for the given model record.
     *
     * @param Model $record The API documentation model instance.
     * @return array The schema for the infolist.
     */
    public static function make(Model $record)
    {
        return (new static)->buildSchema($record->data);
    }

    /**
     * Builds the infolist schema for the provided API data.
     *
     * @param array $data API documentation data.
     * @return array The generated schema.
     */
    protected function buildSchema($data)
    {
        $schema = [];

        // Iterate through each API item and build its section
        foreach ($data as $item) {
            $allParams = collect($item['instructions']['params']); // Collect all parameters
            $parameters = $this->buildParameters($allParams); // Organize parameters by location
            $schema[] = $this->buildSection($item, $parameters, $allParams); // Build the section
        }

        return $schema;
    }

    /**
     * Builds a single section for an API endpoint.
     *
     * @param array $item The API endpoint data.
     * @param array $parameters Organized parameters.
     * @param \Illuminate\Support\Collection $allParams All parameters for the endpoint.
     * @return Section The generated section.
     */
    protected function buildSection($item, $parameters, $allParams)
    {
        return Section::make($item['details']['title'])
            ->columnSpanFull()
            ->description($this->getDescription($item)) // Set section description
            ->headerActions([
                TestApiEndpointAction::make('test_endpoint')
                    ->item($item, $parameters, $allParams) // Add a "Test Endpoint" action
            ])
            ->columns(2)
            ->collapsed(fn() => $item['details']['collapsed']) // Set default collapsed state
            ->schema([
                Grid::make(1)
                    ->schema([$this->buildParametersTabs($parameters, $item['details']['auth_required'])]),
                Grid::make(1)
                    ->schema([
                        Tabs::make()
                            ->columnSpanFull()
                            ->schema($this->buildCodeTabs($item)), // Build code examples
                        ...$this->buildResponses($item), // Include response examples
                    ])
            ])
            ->collapsible(); // Make the section collapsible with two columns
    }

    /**
     * Generates the description for a section based on endpoint details.
     *
     * @param array $item The API endpoint data.
     * @return HtmlString The formatted description.
     */
    protected function getDescription($item)
    {
        $desc = [];

        if (!empty($item['details']['endpoint'])) {
            $desc[] = $item['details']['endpoint']; // Add the endpoint URL
        }

        if (!empty($item['details']['description'])) {
            $desc[] = nl2br(str($item['details']['description'])->markdown()->sanitizeHtml());
        }

        return new HtmlString(implode('<br><br>', $desc)); // Combine descriptions with line breaks
    }

    /**
     * Builds tabs for the request parameters.
     *
     * @param array $parameters Parameters grouped by location.
     * @param bool $authRequired Whether authorization is required.
     * @return Tabs The generated tabs.
     */
    protected function buildParametersTabs($parameters, $authRequired)
    {
        $schema = [];

        foreach ($parameters as $location => $data) {
            $schema[] = Tab::make(ucfirst($location))
                ->label(__($location))
                ->schema([
                    Section::make(__('Always present :parameters', ['parameters' => strtolower(__($location))]))
                        ->description(__(':parameters with * are required!', ['parameters' => ucfirst(__($location))]))
                        ->collapsible()
                        ->schema(function () use ($data, $location, $authRequired) {
                            $alwaysVisible = $data['always_visible'];
                            if ($location === 'header' && $authRequired) {
                                $alwaysVisible['Authorization*: Bearer $API_TOKEN'] = '';
                            }
                            return [
                                KeyValueEntry::make('always_visible.' . strtolower($location))
                                    ->hiddenLabel()
                                    ->keyLabel(__(':parameter & Value', ['parameter' => ucfirst($location)]))
                                    ->valueLabel('Description')
                                    ->state($alwaysVisible),
                            ];
                        }),
                    Section::make(__('Conditional parameters'))
                        ->description(__('Parameters with * are required!'))
                        ->collapsible()
                        ->collapsed()
                        ->hidden(empty($data['conditionally_visible']))
                        ->schema($this->buildConditionalParametersComponents($data['conditionally_visible'], $location))
                ]);
        }

        return Tabs::make()->schema($schema);
    }

    /**
     * Builds code example tabs for an API endpoint.
     *
     * @param array $item The API endpoint data.
     * @return array The generated code example tabs.
     */
    protected function buildCodeTabs($item)
    {
        $tabs = [];
        $predefinedExamples = PredefineCodeBuilderAction::handle($item);

        foreach ($predefinedExamples as $predefinedApp => $predefinedExample) {
            $tabs[$predefinedApp] = Tab::make($predefinedApp)
                ->schema([
                    Code::make('custom_code.body')
                        ->state($predefinedExample['code'])
                        ->language($predefinedExample['style'])
                        ->hiddenLabel(),
                ]);
        }

        if ($item['request_code']['use_custom_codes'] && !empty($item['request_code']['custom_code'])) {
            foreach ($item['request_code']['custom_code'] as $codeExample) {
                $tabs[$codeExample['app_type']] = Tab::make($codeExample['app_type'])
                    ->schema([
                        TextEntry::make('instructions.description')
                            ->hiddenLabel()
                            ->hidden(fn() => empty($codeExample['description']))
                            ->state(new HtmlString($codeExample['description'] ?? ''))
                            ->html(),
                        Code::make('custom_code.body')
                            ->state($codeExample['body'])
                            ->language($codeExample['code_style'])
                            ->hiddenLabel(),
                    ]);
            }
        }

        return array_values($tabs); // Return tabs as an array
    }

    /**
     * Builds response examples for an API endpoint.
     *
     * @param array $item The API endpoint data.
     * @return array The generated response sections.
     */
    protected function buildResponses($item)
    {
        $responses = [];

        if (!empty($item['response'])) {
            foreach ($item['response'] as $response) {
                $responses[] = Section::make($response['title'])
                    ->icon($response['icon'])
                    ->columnSpanFull()
                    ->iconColor(Color::{str($response['color'])->title()->toString()})
                    ->schema([
                        TextEntry::make('response.description')
                            ->hiddenLabel()
                            ->hidden(fn() => empty($response['description']))
                            ->state(new HtmlString(nl2br($response['description'])))
                            ->html(),
                        Code::make('response.code')
                            ->language('json')
                            ->hiddenLabel()
                            ->state($response['body'])
                    ])
                    ->collapsible()
                    ->collapsed();
            }
        }

        return $responses;
    }

    /**
     * Determines if a parameter is always visible.
     *
     * @param \Illuminate\Support\Collection $allParams All parameters.
     * @param array $param The parameter being checked.
     * @return bool Whether the parameter is always visible.
     */
    protected function isParamAlwaysVisible($allParams, $param): bool
    {
        $condParam = ($param['visible'] == 'always' || !isset($param['visibility_condition_param'])) ? null : $allParams->where('name', '=', $param['visibility_condition_param'])->first();
        return ($param['visible'] === 'always' || ($param['visible'] === 'conditionally' && !is_null($condParam) && $condParam['value'] === $param['visibility_condition_value']));
    }

    /**
     * Builds conditional parameter components.
     */
    protected function buildConditionalParametersComponents($parameters, $location)
    {
        $components = [];

        foreach ($parameters as $hint => $params) {
            $components[] = KeyValueEntry::make('conditionally_visible.' . strtolower($location))
                ->hint(__("Present when :hint", ['hint' => $hint]))
                ->hiddenLabel()
                ->keyLabel(__('Parameter & Value'))
                ->valueLabel(__('Description'))
                ->state(collect($params)->mapWithKeys(fn($param) => [($param['required'] ? $param['name'] . "*" : $param['name']) . ': ' . $param['value'] => $param['description']]));
        }

        return $components;
    }

    /**
     * Organizes parameters by their locations (header, route, query, body).
     */
    protected function buildParameters($params)
    {
        $parameters = [
            'header' => [
                'params' => [],
                'always_visible' => [],
                'conditionally_visible' => []
            ],
            'route' => [
                'params' => [],
                'always_visible' => [],
                'conditionally_visible' => []
            ],
            'query' => [
                'params' => [],
                'always_visible' => [],
                'conditionally_visible' => []
            ],
            'body' => [
                'params' => [],
                'always_visible' => [],
                'conditionally_visible' => []
            ],
        ];

        foreach ($params as $param) {
            $location = $param['param_location'];

            if ($this->isParamAlwaysVisible($params, $param)) {
                $parameters[$location]['params'][$param['name']] = $param;
            }

            if ($param['visible'] == 'always') {
                $parameters[$location]['always_visible'][($param['required'] ? $param['name'] . "*" : $param['name']) . ': ' . $param['value']] = $param['description'];
            } else {
                $key = __('`:param` is equal to `:value`', [
                    'param' => $param['visibility_condition_param'],
                    'value' => $param['visibility_condition_value']
                ]);

                if (!isset($parameters[$location]['conditionally_visible'][$key])) {
                    $parameters[$location]['conditionally_visible'][$key] = [];
                }

                $parameters[$location]['conditionally_visible'][$key][] = $param;
            }
        }

        return $parameters;
    }
}