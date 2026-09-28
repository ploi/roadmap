@php
    $tools = collect(App\Mcp\Servers\RoadmapServer::TOOLS)->map(fn (string $tool) => app($tool));
@endphp

<dl {{ $attributes->merge(['class' => 'divide-y divide-gray-200 dark:divide-white/10']) }}>
    @foreach($tools as $tool)
        <div class="py-3 first:pt-0 last:pb-0">
            <dt><code class="font-mono text-sm font-medium text-gray-950 dark:text-white">{{ $tool->name() }}</code></dt>
            <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $tool->description() }}</dd>
        </div>
    @endforeach
</dl>
