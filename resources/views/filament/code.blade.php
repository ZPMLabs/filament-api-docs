@php
    $codeID = 'code-' . str()->uuid();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry" wire:ignore>
    <div 
        x-data="{
            buttonText: 'Copy',
            codeElID: '{{ $codeID }}',
            copy() {
                const code = document.querySelector(`#${this.codeElID}`).textContent;
                navigator.clipboard.writeText(code);

                // Update buttonText to 'Copied'
                this.buttonText = 'Copied';

                // Use a delayed reset after 1 second
                setTimeout(() => {
                    this.buttonText = 'Copy';
                }, 1000);
            }
        }"
    >
        <pre class="relative"><button @click="copy()" x-text="buttonText" class="absolute top-1 right-1 text-xs text-gray-500" style="right: 10px"></button><code id="{{ $codeID }}">{{ $getValue() }}</code></pre>
    </div>
</x-dynamic-component>
