@props(['code', 'label' => null])

<div class="space-y-1" x-data="{ copied: false }">
    <div class="flex items-end justify-between gap-2">
        <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $label }}</p>

        <button type="button"
                class="rounded-md px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:text-gray-300 dark:ring-white/10 dark:hover:bg-white/5"
                x-on:click="navigator.clipboard.writeText($refs.code.innerText); copied = true; setTimeout(() => copied = false, 2000)">
            <span x-show="! copied">{{ trans('mcp.copy') }}</span>
            <span x-show="copied" x-cloak>{{ trans('mcp.copied') }}</span>
        </button>
    </div>

    <pre class="overflow-x-auto rounded-lg bg-gray-50 p-3 font-mono text-sm text-gray-700 ring-1 ring-gray-950/5 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10"><code x-ref="code">{{ $code }}</code></pre>
</div>
