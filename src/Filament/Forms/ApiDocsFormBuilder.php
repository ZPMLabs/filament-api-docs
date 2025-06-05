<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Forms;

use ZPMLabs\FilamentApiDocsBuilder\Actions\PredefineCodeBuilderAction;
use ZPMLabs\FilamentApiDocsBuilder\Enums\CodeStyle;
use ZPMLabs\FilamentApiDocsBuilder\Enums\HttpStatuses;
use ZPMLabs\FilamentApiDocsBuilder\Enums\RequestType;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Guava\FilamentIconPicker\Forms\IconPicker;

class ApiDocsFormBuilder {
    public static function make () {
        return [
            static::mainDetails(),
            static::builder(),
        ];
    }

    public static function builder () {
        return Forms\Components\Section::make(__('Docs & Endpoints'))
        ->schema([
            Forms\Components\Repeater::make('data')
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
                    Forms\Components\Tabs::make()
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
        return Forms\Components\Section::make('Main Details')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->columnSpanFull()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Forms\Components\TextInput::make('version')
                            ->default('1')
                            ->columnSpanFull()
                            ->required(),
                    ])->columnSpan(1),
                Forms\Components\MarkdownEditor::make('description'),
            ])->columns(2);
    }

    public static function parametersTab (): tab {
        $predefinedParams = collect(config('filament-api-docs-builder.predefined_params'));

        $predefinedParamsOptions = $predefinedParams->mapWithKeys(function ($item) {
            return [$item['name'] => $item['name'] . ': ' . $item['value']];
        });

        return Forms\Components\Tabs\Tab::make(__('Parameters'))
            ->schema([
                Forms\Components\Repeater::make('instructions.params')
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
                        Forms\Components\Grid::make()
                            ->columnSpan(1)
                            ->columns()
                            ->schema([
                                Forms\Components\Select::make('predefined_params')
                                    ->label(__('Predefined parameters'))
                                    ->live()
                                    ->hidden($predefinedParams->count() === 0)
                                    ->afterStateUpdated(function ($state, callable $set) use ($predefinedParams) {
                                        
                                        if ($state && $param = $predefinedParams->where('name', $state)->first()) {
                                            dump($param);
                                
                                            // Set the corresponding fields based on the selected param
                                            $set('param_location', $param['location']);
                                            $set('param_type', $param['type']);
                                            $set('name', $param['name']);
                                            $set('value', $param['value']);
                                            $set('required', $param['required']);
                                        }
                                    })
                                    ->options($predefinedParamsOptions),
                                Forms\Components\Select::make('param_location')
                                    ->label(__('Param location'))
                                    ->required()
                                    ->options([
                                        'route' => 'Route param',
                                        'query' => 'Query param',
                                        'body' => 'Body param',
                                        'header' => 'Header param',
                                    ]),
                                Forms\Components\Select::make('param_type')
                                    ->label(__('Param type'))
                                    ->required()
                                    ->options([
                                        'string' => 'String',
                                        'number' => 'Number',
                                        'boolean' => 'Boolean'
                                    ]),
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->label(__('Value'))
                                    ->required(),
                                Forms\Components\Select::make('visible')
                                    ->label(__('Visible'))
                                    ->options([
                                        'always' => 'Always',
                                        'conditionally' => 'Conditionally'
                                    ])
                                    ->default('always')
                                    ->live()
                                    ->required(),
                                Forms\Components\TextInput::make('visibility_condition_param')
                                    ->label(__('Visibility condition parameter'))
                                    ->hidden(fn (Get $get) => $get('visible') === 'always')
                                    ->required(),
                                Forms\Components\TextInput::make('visibility_condition_value')
                                    ->label(__('Visibility condition value'))
                                    ->hidden(fn (Get $get) => $get('visible') === 'always')
                                    ->required(),
                                Forms\Components\Toggle::make('required')
                                    ->label(__('Required')),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(12),
                    ])
            ]);
    }

    public static function detailsTab (): Tab {
        return Forms\Components\Tabs\Tab::make(__('Details'))
            ->columns()
            ->schema([
                Forms\Components\Grid::make()
                    ->columnSpan(1)
                    ->columns()
                    ->schema([
                        Forms\Components\TextInput::make('details.title')
                            ->label(__('Title'))
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('details.request_type')
                            ->label(__('Request Type'))
                            ->columnSpan(1)
                            ->options(RequestType::toArray())
                            ->required(),
                        Forms\Components\TextInput::make('details.endpoint')
                            ->label(__('Endpoint'))
                            ->columnSpanFull()
                            ->live()
                            ->url(),
                        Forms\Components\Toggle::make('details.auth_required')
                            ->label(__('Auth is required for this endpoint'))
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('details.collapsed')
                            ->columnSpan(1)
                            ->label(__('Collapsed by default?')),
                    ]),
                Forms\Components\MarkdownEditor::make('details.description'),
            ]);
    }

    public static function requestCodeTab (): Tab {
        return Forms\Components\Tabs\Tab::make(__('Request Code'))
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('request_code.use_predefined_codes')
                    ->options(PredefineCodeBuilderAction::toArray())
                    ->columnSpan(1)
                    ->multiple()
                    ->label(__('Predefined code examples')),
                Forms\Components\Toggle::make('request_code.use_custom_codes')
                ->columnSpan(1)
                    ->label(__('Custom code examples'))
                    ->live()
                    ->default(false),
                Forms\Components\Repeater::make('request_code.custom_code')
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
                        Forms\Components\Grid::make()
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\TextInput::make('app_type')
                                    ->label(__('Lang'))
                                    ->hint(__('ex: cURL, PHP, Java...'))
                                    ->columnSpanFull()
                                    ->required(),
                                Forms\Components\Select::make('code_style')
                                    ->label(__('Code style'))
                                    ->columnSpanFull()
                                    ->options(CodeStyle::class)
                                    ->required(),
                            ]),
                        Forms\Components\Textarea::make('body')
                            ->label(__('Body'))
                            ->rows(5),
                    ])
        ]);
    }

    public static function responsesTab (): Tab {
        return Forms\Components\Tabs\Tab::make('Responses')
        ->schema([
            Forms\Components\Repeater::make('response')
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
                    Forms\Components\Grid::make()
                        ->columnSpan(1)
                        ->columns(2)
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label(__('Status'))
                                ->columnSpan(1)
                                ->options(HttpStatuses::class)
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
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->label(__('Title'))
                                ->columnSpan(1),
                            Forms\Components\Textarea::make('description')
                                ->rows(8)
                                ->label(__('Description'))
                                ->columnSpanFull(),
                            IconPicker::make('icon')
                                ->required()
                                ->label(__('Icon'))
                                ->columnSpan(1),
                            Forms\Components\Select::make('color')
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
                    Forms\Components\Textarea::make('body')
                        ->rows(18)
                        ->label(__('Result'))
                ])
        ]);
    }
}