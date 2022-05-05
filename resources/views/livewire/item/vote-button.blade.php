<div class="flex items-center space-x-4 p-1">
    <x-filament::button :color="$hasVoted ? 'primary' : 'secondary'" wire:click="toggleUpvote">
        @if($hasVoted)
            <x-heroicon-o-thumb-up class="w-5 h-5"/>
        @else
            <x-heroicon-o-thumb-up class="w-5 h-5"/>
        @endif
    </x-filament::button>

    <span>{{ $item->total_votes }} total {{ trans_choice('messages.votes', $item->total_votes) }}</span>
</div>
