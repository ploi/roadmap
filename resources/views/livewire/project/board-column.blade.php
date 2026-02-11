<section class="h-full">
    <div class="bg-gray-100 rounded-xl min-w-[18rem] lg:w-92 flex flex-col max-h-full dark:bg-white/5">
        <div
            class="p-2 font-semibold border-b border-gray-200 bg-gray-100/80 rounded-t-xl backdrop-blur-xl backdrop-saturate-150 dark:bg-gray-900 dark:text-white dark:border-b-gray-800">
            <div class="flex items-center justify-between">
                <a
                    href="{{ route('projects.boards.show', [$project, $board]) }}"
                    class="border-b border-dotted border-black text-gray-800 dark:text-white">
                    {{ $board->title }}
                </a>

                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-200 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition"
                    >
                        <x-heroicon-o-adjustments-vertical class="w-4 h-4" />
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        class="absolute right-0 z-50 mt-1 w-52 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                    >
                        <div class="p-2">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="{{ trans('general.search-items') }}"
                                class="w-full rounded-md border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                            />
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <div class="py-1">
                                <button
                                    type="button"
                                    wire:click="setSortBy('created_at')"
                                    @click="open = false"
                                    @class([
                                        'flex w-full items-center gap-2 px-4 py-2 text-sm whitespace-nowrap hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'text-brand-500 font-medium' => $sortBy === 'created_at',
                                        'text-gray-700 dark:text-gray-300' => $sortBy !== 'created_at',
                                    ])
                                >
                                    {{ trans('general.sort-newest') }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="setSortBy('total_votes')"
                                    @click="open = false"
                                    @class([
                                        'flex w-full items-center gap-2 px-4 py-2 text-sm whitespace-nowrap hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'text-brand-500 font-medium' => $sortBy === 'total_votes',
                                        'text-gray-700 dark:text-gray-300' => $sortBy !== 'total_votes',
                                    ])
                                >
                                    {{ trans('general.sort-most-voted') }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="setSortBy('last_commented')"
                                    @click="open = false"
                                    @class([
                                        'flex w-full items-center gap-2 px-4 py-2 text-sm whitespace-nowrap hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'text-brand-500 font-medium' => $sortBy === 'last_commented',
                                        'text-gray-700 dark:text-gray-300' => $sortBy !== 'last_commented',
                                    ])
                                >
                                    {{ trans('general.sort-last-commented') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="p-2 space-y-2 o overflow-y-auto flex-1 min-h-0">
            @forelse($items as $item)
                <li>
                    <a href="{{ route('projects.items.show', [$project, $item]) }}"
                       class="block p-4 space-y-4 bg-white shadow rounded-xl hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-950">
                        <div class="flex justify-between">
                            <p>
                                {{ $item->title }}
                            </p>

                            <div class="flex items-center">
                                @if($item->isPrivate())
                                    <span x-data x-tooltip.raw="{{ trans('items.item-private') }}">
                                        <x-heroicon-s-lock-closed class="text-gray-500 fill-gray-500 w-5 h-5" />
                                    </span>
                                @endif
                                @if($item->isPinned())
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         x-data
                                         x-tooltip.raw="{{ trans('items.item-pinned') }}"
                                         class="text-gray-500 fill-gray-500">
                                        <path
                                            d="M15 11.586V6h2V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2h2v5.586l-2.707 1.707A.996.996 0 0 0 6 14v2a1 1 0 0 0 1 1h4v3l1 2 1-2v-3h4a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L15 11.586z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <footer class="flex items-end justify-between">
                            <span
                                class="inline-flex items-center justify-center h-6 px-2 text-sm font-semibold tracking-tight text-gray-700 dark:text-gray-300 rounded-full bg-gray-50 dark:bg-gray-600">
                                {{ $item->created_at->isoFormat('ll') }}
                            </span>

                            <div class="text-gray-500 text-sm">
                                {{ $item->total_votes }} {{ trans_choice('messages.votes', $item->total_votes) }}
                            </div>
                        </footer>
                    </a>
                </li>
            @empty
                <li>
                    <div
                        class="p-3 font-medium text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-400 border-dashed rounded-xl opacity-70">
                        <p>{{ trans('items.no-items') }}</p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</section>
