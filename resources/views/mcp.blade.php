@section('title', trans('mcp.title'))
@section('description', trans('mcp.description', ['app' => config('app.name')]))

@php
    $endpoint = url('mcp');
    $claudeCodeCommand = 'claude mcp add --transport http ' . str(config('app.name'))->slug() . ' ' . $endpoint . ' --header "Authorization: Bearer YOUR_TOKEN"';
    $jsonConfig = json_encode([
        'mcpServers' => [
            (string) str(config('app.name'))->slug() => [
                'type' => 'http',
                'url' => $endpoint,
                'headers' => ['Authorization' => 'Bearer YOUR_TOKEN'],
            ],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
@endphp

<x-app :breadcrumbs="[
    ['title' => trans('mcp.title'), 'url' => route('mcp.docs')]
]">
    <div class="max-w-3xl space-y-6">
        @unless($enabled)
            <div class="rounded-xl bg-yellow-50 p-4 text-sm text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-300 dark:ring-yellow-400/20">
                {{ trans('mcp.disabled') }}
            </div>
        @endunless

        <div class="space-y-2">
            <h1 class="text-2xl tracking-tight font-bold">{{ trans('mcp.title') }}</h1>
            <p class="text-gray-500">{{ trans('mcp.description', ['app' => config('app.name')]) }}</p>
        </div>

        <x-card class="p-4 space-y-2">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('mcp.permissions-title') }}</h2>
            <p class="text-sm text-gray-500">{{ trans('mcp.permissions') }}</p>
        </x-card>

        <x-card class="p-4 space-y-3">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('mcp.step-token-title') }}</h2>
            <p class="text-sm text-gray-500">{{ trans('mcp.step-token') }}</p>

            <div>
                @auth
                    <x-filament::button tag="a" :href="route('profile')" icon="heroicon-o-key">
                        {{ trans('mcp.step-token-button') }}
                    </x-filament::button>
                @else
                    <x-filament::button tag="a" :href="route('login')" icon="heroicon-o-arrow-right-on-rectangle">
                        {{ trans('mcp.step-token-login') }}
                    </x-filament::button>
                @endauth
            </div>
        </x-card>

        <x-card class="p-4 space-y-4">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('mcp.step-connect-title') }}</h2>
            <p class="text-sm text-gray-500">{!! trans('mcp.step-connect') !!}</p>

            <div class="space-y-1">
                <p class="text-sm font-medium">{{ trans('mcp.endpoint') }}</p>
                <code class="block select-all break-all rounded-lg bg-gray-50 p-3 font-mono text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $endpoint }}</code>
            </div>

            <div class="space-y-1">
                <p class="text-sm font-medium">{{ trans('mcp.claude-code') }}</p>
                <pre class="overflow-x-auto rounded-lg bg-gray-50 p-3 font-mono text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300"><code class="select-all">{{ $claudeCodeCommand }}</code></pre>
            </div>

            <div class="space-y-1">
                <p class="text-sm font-medium">{{ trans('mcp.json-config') }}</p>
                <pre class="overflow-x-auto rounded-lg bg-gray-50 p-3 font-mono text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300"><code class="select-all">{{ $jsonConfig }}</code></pre>
            </div>
        </x-card>

        <x-card class="p-4 space-y-2">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('mcp.step-use-title') }}</h2>
            <p class="text-sm text-gray-500">{{ trans('mcp.step-use') }}</p>
        </x-card>

        <x-card class="p-4 space-y-3">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('mcp.tools-title') }}</h2>

            <dl class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach($tools as $tool)
                    <div class="py-3 first:pt-0 last:pb-0">
                        <dt><code class="font-mono text-sm font-medium">{{ $tool->name() }}</code></dt>
                        <dd class="mt-1 text-sm text-gray-500">{{ $tool->description() }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-card>
    </div>
</x-app>
