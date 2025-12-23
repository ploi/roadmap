<div>
    <x-filament::modal id="vote-history-modal" width="2xl">
        <x-slot name="trigger">
            <button
                type="button"
                class="text-sm text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 transition-colors cursor-pointer"
                x-data
                x-tooltip.raw="{{ trans('items.view-vote-history') }}"
            >
                <x-heroicon-o-chart-bar class="w-5 h-5" />
            </button>
        </x-slot>

        @if($this->item->votes()->exists())
            @livewire(\App\Filament\Widgets\VoteHistoryChart::class, ['item' => $this->item])
        @else
            <x-filament::section>
                <x-slot name="heading">
                    {{ trans('items.vote-history') }}
                </x-slot>
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    {{ trans('items.no-vote-history') }}
                </div>
            </x-filament::section>
        @endif
    </x-filament::modal>
</div>
