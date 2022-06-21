<div class="flex items-center space-x-4 p-1">
    @if($item->board?->block_votes)
        <x-filament::button
            color="secondary"
            disabled
        >
            <x-heroicon-o-thumb-up class="w-5 h-5"/>
        </x-filament::button>
    @else
        <x-filament::button
            :color="$vote ? 'primary' : 'secondary'"
            wire:click="toggleUpvote"
        >
            <x-heroicon-o-thumb-up class="w-5 h-5"/>
        </x-filament::button>
    @endif

    <span>{{ trans_choice('messages.total-votes', $item->total_votes, ['votes' => $item->total_votes]) }}</span>

    @if($vote)
        @if($vote->subscribed)
            <button class="border-b border-dotted font-semibold border-gray-500" x-data
                    x-tooltip.raw="{{ trans('items.unsubscribe-tooltip') }}" wire:click="unsubscribe">
                {{ trans('items.unsubscribe') }}
            </button>
        @else
            <button class="border-b border-dotted font-semibold border-gray-500" x-data
                    x-tooltip.raw="{{ trans('items.subscribe-tooltip') }}" wire:click="subscribe">
                {{ trans('items.subscribe') }}
            </button>
        @endif
    @endif

</div>
