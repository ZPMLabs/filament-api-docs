<div>
    @if (!empty($getState()['status']))
    <x-filament::badge color="{{ $getState()['color'] }}" icon="{{ $getState()['icon'] }}">
        {{ $getState()['text'] }}
    </x-filament::badge>
    @endif
    <div class="overflow-auto" style="max-height: 500px">
        <pre><code>{{ str($getState()['response'])->toHtmlString() }}</code></pre>
    </div>
</div>