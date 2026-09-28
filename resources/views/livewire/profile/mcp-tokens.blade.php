<div class="space-y-4">
    <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.mcp.heading') }}</h2>

    <p class="text-gray-500 text-sm max-w-2xl">
        {{ trans('profile.mcp.description') }}
        <a href="{{ route('mcp.docs') }}" class="font-medium text-brand-600 hover:text-brand-500">{{ trans('profile.mcp.read_docs') }}</a>
    </p>

    <div class="text-sm text-gray-500">
        {{ trans('profile.mcp.endpoint') }}:
        <code class="select-all font-mono text-gray-700 dark:text-gray-300">{{ url('mcp') }}</code>
    </div>

    @if($this->newMcpToken)
        <div class="space-y-2 rounded-lg bg-gray-50 p-4 dark:bg-gray-900 max-w-2xl">
            <p class="text-sm font-medium">{{ trans('profile.mcp.new_token') }}</p>
            <code class="block break-all select-all font-mono text-sm text-gray-700 dark:text-gray-300">{{ $this->newMcpToken }}</code>
        </div>
    @endif

    @if($mcpTokens->isEmpty())
        <p class="text-sm text-gray-500">{{ trans('profile.mcp.no_tokens') }}</p>
    @else
        <ul class="divide-y divide-gray-200 dark:divide-white/10 max-w-2xl">
            @foreach($mcpTokens as $token)
                <li class="flex items-center justify-between gap-4 py-2" wire:key="mcp-token-{{ $token->id }}">
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ $token->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $token->last_used_at ? trans('profile.mcp.last_used', ['date' => $token->last_used_at->diffForHumans()]) : trans('profile.mcp.never_used') }}
                        </p>
                    </div>

                    {{ ($this->revokeMcpTokenAction)(['token' => $token->id]) }}
                </li>
            @endforeach
        </ul>
    @endif

    <div>
        {{ $this->createMcpTokenAction }}
    </div>
</div>
