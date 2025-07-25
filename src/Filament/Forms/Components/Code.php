<?php

namespace ZPMLabs\FilamentApiDocsBuilder\Filament\Forms\Components;

use Closure;
use Filament\Infolists\Components\Concerns;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Illuminate\Support\HtmlString;
use Tempest\Highlight\Highlighter;
use Tempest\Highlight\Themes\InlineTheme;

/**
 * Class Code
 *
 * A custom Filament form component for displaying code snippets with syntax highlighting.
 * It supports various languages, with JSON as the default.
 */
class Code extends Entry implements HasAffixActions
{
    use Concerns\CanFormatState; // Allows state formatting capabilities.
    use Concerns\HasAffixes;     // Provides affix functionality for actions.

    /**
     * The Blade view associated with this component.
     *
     * @var string
     */
    protected string $view = 'filament-api-docs-builder::filament.code';

    /**TestApiEndpointAction
     * The programming language for syntax highlighting.
     *
     * @var string|Closure|null
     */
    protected string | Closure | null $language = 'json';

    /**
     * Get the value of the state, formatted as highlighted code.
     *
     * @return HtmlString Highlighted code wrapped in an HTML-safe string.
     */
    public function getValue()
    {
        $state = $this->getState(); // Retrieve the current state of the component.

        $language = $this->language; // Determine the language for highlighting.

        // Highlight the code using the Tempest library and return as HTML-safe string.
        return new HtmlString(
            (new Highlighter(
                new InlineTheme(__DIR__ . '/../../../../resources/themes/solarized-dark.css')
            ))->parse($state, $language)
        );
    }

    /**
     * Set the language for syntax highlighting.
     *
     * @param string|Closure $language The programming language.
     * @return static
     */
    public function language(string | Closure $language): static
    {
        $this->language = $language;

        return $this;
    }
}