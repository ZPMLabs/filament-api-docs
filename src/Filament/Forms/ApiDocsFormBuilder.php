<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Forms;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use ZPMLabs\FilamentApiDocsBuilder\Actions\PredefineCodeBuilderAction;
use ZPMLabs\FilamentApiDocsBuilder\Enums\CodeStyle;
use ZPMLabs\FilamentApiDocsBuilder\Enums\HttpStatuses;
use ZPMLabs\FilamentApiDocsBuilder\Enums\RequestType;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use ZPMLabs\FilamentIconPicker\Forms\IconPicker;

class ApiDocsFormBuilder {
    public static function make () {
        return [
            static::mainDetails(),
            static::builder(),
        ];
    }

    public static function builder () {
        return Section::make(__('Docs & Endpoints'))
        ->columnSpanFull()
        ->schema([
            Repeater::make('data')
                ->hiddenLabel()
                ->cloneable()
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )
                ->addActionLabel(__('Add Section'))
                ->itemLabel(function (array $state): ?string {
                    if (!empty($state['details']) && !empty($state['details']['title'])) {
                        return $state['details']['title'];
                    }

                    return __('Endpoint Section');
                })
                ->collapsible()
                ->collapsed(function (): bool {
                    static $position = 1;
                    return $position++ > 1;
                })
                ->schema([
                    Tabs::make()
                        ->schema([
                            static::detailsTab(),
                            static::parametersTab(),
                            static::requestCodeTab(),
                            static::responsesTab(),
                        ])

                ])
        ]);
    }

    public static function mainDetails () {
        return Section::make('Main Details')
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('title')
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('slug')
                            ->columnSpanFull()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        TextInput::make('version')
                            ->default('1')
                            ->columnSpanFull()
                            ->required(),
                    ])->columnSpan(1),
                MarkdownEditor::make('description'),
            ])
            ->columnSpanFull();
    }

    public static function parametersTab (): Tab {
        $predefinedParams = collect(config('filament-api-docs-builder.predefined_params'));

        $predefinedParamsOptions = $predefinedParams->mapWithKeys(function ($item) {
            return [$item['name'] => $item['name'] . ': ' . $item['value']];
        });

        return Tab::make(__('Parameters'))
            ->schema([
                Repeater::make('instructions.params')
                    ->cloneable()
                    ->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation(),
                    )
                    ->hiddenLabel()
                    ->columns()
                    ->itemLabel(function (array $state): ?string {
                        if (!empty($state['name'])) {
                            return $state['name'] . ($state['required'] ? __(' - required') : __(' - optional'));
                        }

                        return __('Parameter Section');
                    })
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make()
                            ->columnSpan(1)
                            ->columns()
                            ->schema([
                                Select::make('predefined_params')
                                    ->label(__('Predefined parameters'))
                                    ->live()
                                    ->hidden($predefinedParams->count() === 0)
                                    ->afterStateUpdated(function ($state, callable $set) use ($predefinedParams) {
                                        
                                        if ($state && $param = $predefinedParams->where('name', $state)->first()) {
                                
                                            // Set the corresponding fields based on the selected param
                                            $set('param_location', $param['location']);
                                            $set('param_type', $param['type']);
                                            $set('name', $param['name']);
                                            $set('value', $param['value']);
                                            $set('required', $param['required']);
                                        }
                                    })
                                    ->options($predefinedParamsOptions),
                                Select::make('param_location')
                                    ->label(__('Param location'))
                                    ->required()
                                    ->options([
                                        'route' => 'Route param',
                                        'query' => 'Query param',
                                        'body' => 'Body param',
                                        'header' => 'Header param',
                                    ]),
                                Select::make('param_type')
                                    ->label(__('Param type'))
                                    ->required()
                                    ->options([
                                        'string' => 'String',
                                        'number' => 'Number',
                                        'boolean' => 'Boolean'
                                    ]),
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),
                                TextInput::make('value')
                                    ->label(__('Value'))
                                    ->required(),
                                Select::make('visible')
                                    ->label(__('Visible'))
                                    ->options([
                                        'always' => 'Always',
                                        'conditionally' => 'Conditionally'
                                    ])
                                    ->default('always')
                                    ->live()
                                    ->required(),
                                TextInput::make('visibility_condition_param')
                                    ->label(__('Visibility condition parameter'))
                                    ->hidden(fn (Get $get) => $get('visible') === 'always')
                                    ->required(),
                                TextInput::make('visibility_condition_value')
                                    ->label(__('Visibility condition value'))
                                    ->hidden(fn (Get $get) => $get('visible') === 'always')
                                    ->required(),
                                Toggle::make('required')
                                    ->label(__('Required')),
                            ]),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(12),
                    ])
            ]);
    }

    public static function detailsTab (): Tab {
        return Tab::make(__('Details'))
            ->columns()
            ->schema([
                Grid::make()
                    ->columnSpan(1)
                    ->columns()
                    ->schema([
                        TextInput::make('details.title')
                            ->label(__('Title'))
                            ->required()
                            ->columnSpan(1),
                        Select::make('details.request_type')
                            ->label(__('Request Type'))
                            ->columnSpan(1)
                            ->options(RequestType::toArray())
                            ->required(),
                        TextInput::make('details.endpoint')
                            ->label(__('Endpoint'))
                            ->columnSpanFull()
                            ->live()
                            ->url(),
                        Toggle::make('details.auth_required')
                            ->label(__('Auth is required for this endpoint'))
                            ->columnSpan(1),
                        Toggle::make('details.collapsed')
                            ->columnSpan(1)
                            ->label(__('Collapsed by default?')),
                    ]),
                MarkdownEditor::make('details.description'),
            ]);
    }

    public static function requestCodeTab (): Tab {
        return Tab::make(__('Request Code'))
            ->columns(2)
            ->schema([
                Select::make('request_code.use_predefined_codes')
                    ->options(PredefineCodeBuilderAction::toArray())
                    ->columnSpan(1)
                    ->multiple()
                    ->label(__('Predefined code examples')),
                Toggle::make('request_code.use_custom_codes')
                ->columnSpan(1)
                    ->label(__('Custom code examples'))
                    ->live()
                    ->default(false),
                Repeater::make('request_code.custom_code')
                    ->label(__('Custom code'))
                    ->columnSpanFull()
                    ->cloneable()
                    ->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation(),
                    )
                    ->itemLabel(function (array $state): ?string {
                        return __($state['app_type']) ?? __('Custom Code Section');
                    })
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (Get $get) => $get('request_code.use_custom_codes') === true)
                    ->schema([
                        Grid::make()
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('app_type')
                                    ->label(__('Lang'))
                                    ->hint(__('ex: cURL, PHP, Java...'))
                                    ->columnSpanFull()
                                    ->required(),
                                Select::make('code_style')
                                    ->label(__('Code style'))
                                    ->columnSpanFull()
                                    ->options(CodeStyle::class)
                                    ->required(),
                            ]),
                        Textarea::make('body')
                            ->label(__('Body'))
                            ->rows(5),
                    ])
        ]);
    }

    public static function responsesTab (): Tab {
        return Tab::make('Responses')
        ->schema([
            Repeater::make('response')
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )
                ->addActionLabel(__('Add Response'))
                ->itemLabel(function (array $state): ?string {
                    return $state['title'] ?? __('Response Section');
                })
                ->columns(2)
                ->schema([
                    Grid::make()
                        ->columnSpan(1)
                        ->columns(2)
                        ->schema([
                            Select::make('status')
                                ->label(__('Status'))
                                ->columnSpan(1)
                                ->options(collect(HttpStatuses::cases())
                                ->mapWithKeys(fn (HttpStatuses $status) => [$status->value => $status->getLabel()])
                                ->toArray())
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state && HttpStatuses::tryFrom($state)) {
                                        $status = HttpStatuses::from($state); // Get the enum instance from the code
                                        // Set the corresponding fields based on the selected status
                                        $set('title', $status->getLabel());
                                        $set('color', $status->getColor());
                                        $set('icon', $status->getIcon());
                                    }
                                }),
                            TextInput::make('title')
                                ->required()
                                ->label(__('Title'))
                                ->columnSpan(1),
                            Textarea::make('description')
                                ->rows(8)
                                ->label(__('Description'))
                                ->columnSpanFull(),
                            IconPicker::make('icon')
                                ->required()
                                ->columns(3)
                                ->label(__('Icon'))
                                ->columnSpan(1),
                            Select::make('color')
                                ->allowHtml()
                                ->label(__('Color'))
                                ->columnSpan(1)
                                ->required()
                                ->native(false)
                                ->options(
                                    collect(Color::all())
                                        ->mapWithKeys(static fn ($case, $key) => [
                                            $key => "<span class='flex items-center gap-x-4'>
                                            <span class='rounded-full w-4 h-4' style='background:rgb(" .  Color::{str($key)->title()->toString()}[600] . ")'></span>
                                            <span>" . str($key)->title() . '</span>
                                            </span>',
                                        ]),
                                ),
                            ]),
                    Textarea::make('body')
                        ->rows(18)
                        ->label(__('Result'))
                ])
        ]);
    }
}