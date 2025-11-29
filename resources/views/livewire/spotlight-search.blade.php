<div
    x-data="{
        selectedIndex: -1,

        getTotalResults() {
            const container = this.$refs.resultsContainer;
            if (!container) return 0;
            return container.querySelectorAll('a[href], button').length;
        },

        resetAndFocus() {
            this.selectedIndex = -1;
            this.$nextTick(() => {
                this.$refs.searchInput?.focus();
            });
        },

        handleKeydown(e) {
            const searchInput = this.$refs.searchInput;
            if (!searchInput || document.activeElement !== searchInput) return;

            if (e.key === 'Escape') {
                e.preventDefault();
                this.close();
                return;
            }

            const total = this.getTotalResults();
            if (!total) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectNext();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectPrevious();
            } else if (e.key === 'Enter' && this.selectedIndex >= 0) {
                e.preventDefault();
                this.executeSelected();
            }
        },

        selectNext() {
            const total = this.getTotalResults();
            if (total === 0) return;
            this.selectedIndex = (this.selectedIndex + 1) % total;
            this.scrollToSelected();
        },

        selectPrevious() {
            const total = this.getTotalResults();
            if (total === 0) return;
            this.selectedIndex = this.selectedIndex <= 0 ? total - 1 : this.selectedIndex - 1;
            this.scrollToSelected();
        },

        scrollToSelected() {
            this.$nextTick(() => {
                const selected = this.$refs.resultsContainer?.querySelector('[data-selected=true]');
                if (selected) {
                    selected.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            });
        },

        executeSelected() {
            const selected = this.$refs.resultsContainer?.querySelector('[data-selected=true]');
            if (selected) {
                selected.click();
            }
        },

        isItemSelected(index) {
            return this.selectedIndex === index;
        },

        close() {
            this.selectedIndex = -1;
            $wire.close();
        }
    }"
    @keydown="handleKeydown($event)"
    @spotlight-opened.window="resetAndFocus()"
    x-cloak
>
    {{-- Backdrop Overlay --}}
    <div
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-gray-950/40 dark:bg-gray-950/60 backdrop-blur-sm"
        @click="close()"
    ></div>

    {{-- Spotlight Modal --}}
    <div
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
        class="fixed inset-x-0 top-[10vh] z-50 mx-auto w-full max-w-2xl px-4"
    >
        <div class="spotlight-container overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            {{-- Search Input --}}
            <div class="flex items-center gap-3 border-b border-gray-200 px-4 dark:border-gray-700">
                <x-filament::icon
                    icon="heroicon-o-magnifying-glass"
                    class="h-5 w-5 text-gray-400 dark:text-gray-500"
                />
                <input
                    x-ref="searchInput"
                    type="text"
                    wire:model.live.debounce.300ms="query"
                    placeholder="{{ trans('spotlight.search_placeholder') }}"
                    class="w-full border-0 bg-transparent py-4 text-base text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 dark:text-white dark:placeholder-gray-500"
                    autocomplete="off"
                />
                <kbd class="hidden rounded border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-500 sm:inline-block dark:border-gray-700 dark:text-gray-400">
                    ESC
                </kbd>
            </div>

            {{-- Results Container --}}
            <div x-ref="resultsContainer" class="max-h-[60vh] overflow-y-auto">
                @if($query && ($items || $projects || true))
                    <div class="p-2">
                        {{-- Items Section --}}
                        @if($items)
                            <div class="mb-3">
                                <x-spotlight.section-header :title="trans('spotlight.section_items')" />
                                @foreach($items as $index => $item)
                                    <x-spotlight.result-item
                                        :href="$item['url']"
                                        :index="$index"
                                        icon="heroicon-o-document-text"
                                        :title="$item['title']"
                                    >
                                        <div class="mt-0.5 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if($item['project_title'])
                                                <span class="truncate">{{ $item['project_title'] }}</span>
                                            @endif
                                            @if($item['board_title'])
                                                <span class="truncate">• {{ $item['board_title'] }}</span>
                                            @endif
                                            @if($item['votes_count'])
                                                <span class="flex items-center gap-1">
                                                    <x-filament::icon icon="heroicon-o-arrow-up" class="h-3 w-3" />
                                                    {{ $item['votes_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <x-slot:aside>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $item['created_at'] }}
                                            </span>
                                        </x-slot:aside>
                                    </x-spotlight.result-item>
                                @endforeach
                            </div>
                        @endif

                        {{-- Projects Section --}}
                        @if($projects)
                            <div class="mb-3">
                                <x-spotlight.section-header :title="trans('spotlight.section_projects')" />
                                @foreach($projects as $index => $project)
                                    @php
                                        $projectIndex = count($items) + $index;
                                    @endphp
                                    <x-spotlight.result-item
                                        :href="$project['url']"
                                        :index="$projectIndex"
                                        :icon="$project['icon'] ?: 'heroicon-o-folder'"
                                        :iconClass="$project['icon'] ? 'emoji' : ''"
                                        :title="$project['title']"
                                        :meta="$project['description'] ? Str::limit($project['description'], 80) : null"
                                    >
                                        <x-slot:aside>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ trans_choice('spotlight.board', $project['boards_count'], ['count' => $project['boards_count']]) }}
                                            </span>
                                        </x-slot:aside>
                                    </x-spotlight.result-item>
                                @endforeach
                            </div>
                        @endif

                        {{-- Create New Item Action --}}
                        @if($query)
                            @php
                                $createNewIndex = count($items) + count($projects);
                            @endphp
                            <div>
                                <x-spotlight.section-header :title="trans('spotlight.section_actions')" />
                                <button
                                    type="button"
                                    wire:click="createNewItem"
                                    x-bind:data-selected="isItemSelected({{ $createNewIndex }})"
                                    class="group flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                    x-bind:class="isItemSelected({{ $createNewIndex }}) ? 'bg-blue-50 dark:bg-blue-900/20' : ''"
                                >
                                    <x-filament::icon
                                        icon="heroicon-o-plus-circle"
                                        class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500 dark:text-blue-400"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <div class="font-medium text-blue-600 dark:text-blue-400">
                                            {{ trans('spotlight.create_new_item') }}
                                        </div>
                                        <div class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                            "{{ $query }}"
                                        </div>
                                    </div>
                                </button>
                            </div>
                        @endif
                    </div>
                @elseif($query)
                    {{-- Empty State --}}
                    <div class="px-6 py-14 text-center">
                        <x-filament::icon
                            icon="heroicon-o-magnifying-glass"
                            class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                        />
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ trans('spotlight.no_results') }} "<span class="font-medium">{{ $query }}</span>"
                        </p>
                        <button
                            type="button"
                            wire:click="createNewItem"
                            x-bind:data-selected="isItemSelected(0)"
                            x-init="selectedIndex = 0"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                            x-bind:class="isItemSelected(0) ? 'bg-blue-50 dark:bg-blue-900/20' : ''"
                        >
                            <x-filament::icon icon="heroicon-o-plus" class="h-4 w-4" />
                            {{ trans('spotlight.create_new_item') }}
                        </button>
                    </div>
                @else
                    {{-- Initial State - Quick Actions --}}
                    <div class="p-2">
                        <x-spotlight.section-header :title="trans('spotlight.quick_actions')" />

                        {{-- Create New Item --}}
                        <x-spotlight.result-item
                            :index="0"
                            icon="heroicon-o-plus-circle"
                            :title="trans('spotlight.create_new_item')"
                            :meta="trans('spotlight.create_new_item_desc')"
                            @click="close(); $dispatch('open-submit-item-modal')"
                        />

                        {{-- Profile (authenticated users only) --}}
                        @auth
                            <x-spotlight.result-item
                                :href="route('profile')"
                                :index="1"
                                icon="heroicon-o-user-circle"
                                :title="trans('spotlight.profile_title')"
                                :meta="trans('spotlight.profile_desc')"
                            />
                        @endauth

                        {{-- Activity --}}
                        <x-spotlight.result-item
                            :href="route('activity')"
                            :index="auth()->check() ? 2 : 1"
                            icon="heroicon-o-bolt"
                            :title="trans('spotlight.activity')"
                            :meta="trans('spotlight.activity_desc')"
                        />

                        {{-- Admin Panel (admin users only) --}}
                        @if(auth()->check() && auth()->user()->hasAdminAccess())
                            <x-spotlight.result-item
                                :href="route('filament.admin.pages.dashboard')"
                                :index="3"
                                icon="heroicon-o-cog-6-tooth"
                                :title="trans('spotlight.admin_panel')"
                                :meta="trans('spotlight.admin_panel_desc')"
                            />
                        @endif

                        <div class="mt-4 px-3 py-2 text-center text-xs text-gray-400 dark:text-gray-500">
                            {{ trans('spotlight.keyboard_hint') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer with Keyboard Shortcuts --}}
            @if($query)
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <kbd class="rounded border border-gray-300 px-1.5 py-0.5 font-mono dark:border-gray-600">↑</kbd>
                                <kbd class="rounded border border-gray-300 px-1.5 py-0.5 font-mono dark:border-gray-600">↓</kbd>
                                <span>{{ trans('spotlight.keyboard_navigate') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <kbd class="rounded border border-gray-300 px-1.5 py-0.5 font-mono dark:border-gray-600">↵</kbd>
                                <span>{{ trans('spotlight.keyboard_open') }}</span>
                            </div>
                        </div>
                        <div>
                            {{ trans_choice('spotlight.results_count', $totalResults, ['count' => $totalResults]) }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>