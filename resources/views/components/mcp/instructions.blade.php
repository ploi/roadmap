@php
    $endpoint = url('mcp');
    $serverName = Illuminate\Support\Str::slug(config('app.name')) ?: 'roadmap';
    $json = fn (array $config): string => json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $clients = [
        'claude-code' => [
            'label' => 'Claude Code',
            'steps' => trans('mcp.clients.claude-code'),
            'snippets' => [
                ['label' => trans('mcp.run-in-terminal'), 'code' => "claude mcp add --transport http {$serverName} {$endpoint} --header \"Authorization: Bearer YOUR_TOKEN\""],
            ],
        ],
        'claude-desktop' => [
            'label' => 'Claude Desktop',
            'steps' => trans('mcp.clients.claude-desktop'),
            'snippets' => [
                ['label' => 'claude_desktop_config.json', 'code' => $json(['mcpServers' => [$serverName => [
                    'command' => 'npx',
                    'args' => ['-y', 'mcp-remote', $endpoint, '--header', 'Authorization:${AUTH_HEADER}'],
                    'env' => ['AUTH_HEADER' => 'Bearer YOUR_TOKEN'],
                ]]])],
            ],
            'note' => trans('mcp.clients.claude-web'),
        ],
        'chatgpt' => [
            'label' => 'ChatGPT',
            'steps' => trans('mcp.clients.chatgpt'),
            'snippets' => [
                ['label' => trans('mcp.clients.chatgpt-url'), 'code' => $endpoint],
            ],
            'note' => trans('mcp.clients.chatgpt-note'),
        ],
        'cursor' => [
            'label' => 'Cursor',
            'steps' => trans('mcp.clients.cursor'),
            'snippets' => [
                ['label' => '~/.cursor/mcp.json', 'code' => $json(['mcpServers' => [$serverName => [
                    'url' => $endpoint,
                    'headers' => ['Authorization' => 'Bearer YOUR_TOKEN'],
                ]]])],
            ],
        ],
        'vscode' => [
            'label' => 'VS Code',
            'steps' => trans('mcp.clients.vscode'),
            'snippets' => [
                ['label' => '.vscode/mcp.json', 'code' => $json(['servers' => [$serverName => [
                    'type' => 'http',
                    'url' => $endpoint,
                    'headers' => ['Authorization' => 'Bearer YOUR_TOKEN'],
                ]]])],
            ],
        ],
        'codex' => [
            'label' => 'Codex',
            'steps' => trans('mcp.clients.codex'),
            'snippets' => [
                ['label' => '~/.codex/config.toml', 'code' => "[mcp_servers.{$serverName}]\nurl = \"{$endpoint}\"\nbearer_token_env_var = \"ROADMAP_TOKEN\""],
                ['label' => trans('mcp.run-in-terminal'), 'code' => 'export ROADMAP_TOKEN="YOUR_TOKEN"'],
            ],
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }} x-data="{ client: 'claude-code' }">
    <x-mcp.code :label="trans('mcp.endpoint')" :code="$endpoint" />

    <div class="flex flex-wrap gap-2" role="tablist">
        @foreach($clients as $key => $client)
            <button type="button"
                    role="tab"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium ring-1 transition"
                    x-bind:aria-selected="client === @js($key)"
                    x-bind:class="client === @js($key)
                        ? 'bg-gray-900 text-white ring-gray-900 dark:bg-white dark:text-gray-900 dark:ring-white'
                        : 'bg-white text-gray-700 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-white/10'"
                    x-on:click="client = @js($key)">
                {{ $client['label'] }}
            </button>
        @endforeach
    </div>

    @foreach($clients as $key => $client)
        <div class="space-y-3" x-show="client === @js($key)" @if(! $loop->first) x-cloak @endif>
            <ol class="list-decimal space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-400">
                @foreach($client['steps'] as $step)
                    <li>{!! $step !!}</li>
                @endforeach
            </ol>

            @foreach($client['snippets'] as $snippet)
                <x-mcp.code :label="$snippet['label']" :code="$snippet['code']" />
            @endforeach

            @isset($client['note'])
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client['note'] }}</p>
            @endisset
        </div>
    @endforeach
</div>
