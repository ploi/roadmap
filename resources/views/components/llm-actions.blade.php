@props(['url', 'prompt', 'askAi' => true])

@php
    $buttonClass = 'inline-flex w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-2 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:border-primary-500/40 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 cursor-pointer';
    $askAiQuery = urlencode($prompt);
    $assistants = [
        'ChatGPT' => 'https://chatgpt.com/?hints=search&q=' . $askAiQuery,
        'Claude' => 'https://claude.ai/new?q=' . $askAiQuery,
        'Perplexity' => 'https://www.perplexity.ai/search?q=' . $askAiQuery,
        'Mistral' => 'https://chat.mistral.ai/chat?q=' . $askAiQuery,
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}
     x-data="{
        copied: false,
        open: false,
        async copyForLlms() {
            const url = {{ \Illuminate\Support\Js::from($url) }};
            const markdown = fetch(url, { headers: { Accept: 'text/markdown' } })
                .then(response => {
                    if (! response.ok) {
                        throw new Error(response.status);
                    }

                    return response.text();
                });

            try {
                // Safari drops the click's user activation across an await, so it
                // gets handed the pending text rather than the resolved string.
                await navigator.clipboard.write([
                    new ClipboardItem({ 'text/plain': markdown.then(text => new Blob([text], { type: 'text/plain' })) }),
                ]);
            } catch (error) {
                try {
                    await navigator.clipboard.writeText(await markdown);
                } catch (fallback) {
                    window.open(url, '_blank', 'noopener');

                    return;
                }
            }

            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
     }">
    <button type="button" @click="copyForLlms()" class="flex-1 {{ $buttonClass }}"
            :aria-label="copied ? {{ \Illuminate\Support\Js::from(trans('items.copied-to-clipboard')) }} : {{ \Illuminate\Support\Js::from(trans('items.copy-for-llms-label')) }}">
        <x-heroicon-o-clipboard-document x-show="! copied" class="w-3.5 h-3.5" aria-hidden="true"/>
        <x-heroicon-o-check x-show="copied" x-cloak class="w-3.5 h-3.5 text-primary-500" aria-hidden="true"/>
        <span x-text="copied ? {{ \Illuminate\Support\Js::from(trans('items.copied')) }} : {{ \Illuminate\Support\Js::from(trans('items.copy-for-llms')) }}">{{ trans('items.copy-for-llms') }}</span>
    </button>

    <a href="{{ $url }}" target="_blank" rel="noopener" class="flex-1 {{ $buttonClass }}">
        <x-heroicon-o-code-bracket class="w-3.5 h-3.5" aria-hidden="true"/>
        {{ trans('items.view-as-markdown') }}
    </a>

    @if($askAi)
        <div class="relative flex-1" @keydown.escape.window="open = false">
            <button type="button" @click="open = ! open" @click.outside="open = false" class="{{ $buttonClass }}"
                    :aria-expanded="open" aria-haspopup="true">
                <x-heroicon-o-sparkles class="w-3.5 h-3.5" aria-hidden="true"/>
                {{ trans('items.ask-ai') }}
                <x-heroicon-o-chevron-down class="w-3 h-3 transition-transform duration-200" ::class="open && 'rotate-180'" aria-hidden="true"/>
            </button>

            <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                 class="absolute right-0 z-30 mt-2 w-44 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-1 shadow-lg text-left">
                @foreach($assistants as $name => $assistantUrl)
                    <a href="{{ $assistantUrl }}" target="_blank" rel="noopener noreferrer"
                       class="block rounded-md px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ $name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
