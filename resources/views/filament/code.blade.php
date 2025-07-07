@php
    $codeID = 'code-' . str()->uuid();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry" wire:ignore>
    <div class="overflow-auto"
        x-data="{
            buttonText: 'Copy',
            codeElID: '{{ $codeID }}',
            copy() {
                const code = document.querySelector(`#${this.codeElID}`).textContent;
                navigator.clipboard.writeText(code);
                this.buttonText = 'Copied';
                setTimeout(() => {
                    this.buttonText = 'Copy';
                }, 1000);
            }
        }"
    >
        <div class="flex justify-end mb-1">
            <button 
                @click="copy()" 
                x-text="buttonText"
                class="text-xs bg-primary-100 text-primary-800 font-medium px-2 py-0.5 rounded-full"
            ></button>
        </div>

        <pre><code id="{{ $codeID }}">{{ $getValue() }}</code></pre>
    </div>
</x-dynamic-component>

