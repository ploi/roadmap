@section('title', $project->title)@show
@section('image', $project->getOgImage($project->description, 'Roadmap - Project'))@show
@section('description', $project->description)@show
@section('canonical', route('projects.show', $project))@show

<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project)]
]">
    <div
        @class([
        'w-full h-[calc(100vh-170px)] overflow-x-auto',
        ])
    >
        <div
            @class([
            'inline-flex h-full w-full min-w-full gap-4 flex-nowrap overflow-x-scroll',
            'justify-center' => app(\App\Settings\GeneralSettings::class)->board_centered
            ])
        >
            @forelse($boards as $board)
                <section class="h-full">
                    <div class="bg-gray-100 rounded-xl min-w-[18rem] lg:w-[23rem] flex flex-col max-h-full dark:bg-white/5">
                        <div
                            class="p-2 font-semibold text-center text-gray-800 border-b bg-gray-100/80 rounded-t-xl backdrop-blur-xl backdrop-saturate-150 dark:bg-gray-900 dark:text-white dark:border-b-gray-800">
                            <a
                                href="{{ route('projects.boards.show', [$project, $board]) }}"
                                class="border-b border-dotted border-black">
                                {{ $board->title }}
                            </a>
                        </div>
                        <ul class="p-2 space-y-2 o overflow-y-auto flex-1 min-h-0">
                            @forelse($board->items as $item)
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
            @empty
                <div class="w-full">
                    <div
                        class="flex flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center border rounded-2xl">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-blue-500 bg-white rounded-full shadow dark:bg-gray-900">
                            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                            </svg>
                        </div>

                        <header class="max-w-xs space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight">{{ trans('items.all-caught-up-title') }}</h2>

                            <p class="font-medium text-gray-500 dark:text-gray-400">
                                {{ trans('messages.no-boards') }}
                            </p>
                        </header>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app>
