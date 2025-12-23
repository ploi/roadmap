<div>
    <div class="flex items-center space-x-4 p-1">
        @if($model->board?->block_votes)
            <x-filament::button
                size="xs"
                color="secondary"
                disabled
            >
                <x-heroicon-o-hand-thumb-up class="w-5 h-5"/>
            </x-filament::button>
        @else
            <x-filament::button
                size="xs"
                :color="$vote ? 'gray' : 'primary'"
                wire:click="toggleUpvote"
            >
                @if($vote)
                    <x-heroicon-o-hand-thumb-down class="w-5 h-5"/>
                @else
                    <x-heroicon-o-hand-thumb-up class="w-5 h-5"/>
                @endif
            </x-filament::button>
        @endif

        <span
            class="text-sm">{{ trans_choice('messages.total-votes', $model->total_votes, ['votes' => $model->total_votes]) }}</span>

        <livewire:item.vote-history :item="$model" />

        @if($vote && $showSubscribeOption)
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
    <div class="py-1 mx-2">
        @if(app(\App\Settings\GeneralSettings::class)->show_voter_avatars)
            @if($this->recentVoters->count() > 0)
                <div class="flex -space-x-2">
                    @foreach($this->recentVoters as $voter)
                        <a href="{{ route('public-user', $voter['username']) }}">
                        <img src="{{ $voter['avatar'] }}"
                             class="inline object-cover w-8 h-8 border-2 border-white rounded-full dark:border-gray-800"
                             alt="{{ $voter['name'] }}" x-data x-tooltip.raw="{{ $voter['name'] }}">
                        </a>
                        @if($loop->last && $this->model->votes->count() > $this->recentVotersToShow)
                            <a class="shrink-0 flex items-center justify-center w-8 h-8 text-xs font-medium text-white bg-gray-400 border-2 border-white rounded-full cursor-auto"
                               href="#">+ {{ $this->model->votes->count() - $this->recentVotersToShow }} </a>
                        @endif
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
