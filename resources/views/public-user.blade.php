@section('title', $user->name)

<x-app :breadcrumbs="[
    ['title' => trans('public-user.title'), 'url' => route('public-user', $user->username)],
    ['title' => $user->username, 'url' => route('public-user', $user->username)],
]">
    <section id="about" class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
                <img src="{{ $user->getGravatar('200') }}"
                     class="rounded-full w-32 h-32 border-4 border-gray-100 dark:border-gray-700"
                     alt="{{ $user->name }}" />

                <div class="flex-1 text-center md:text-left">
                    <h1 class="font-semibold text-4xl text-gray-900 dark:text-gray-100 mb-2">
                        {{ $user->name }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
                        {{ trans('public-user.subtitle', ['app' => config('app.name')]) }}
                    </p>

                    <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                @svg('heroicon-o-queue-list', 'w-5 h-5 text-gray-400')
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($data['items_created'] ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans_choice('public-user.quick-stats-items', $data['items_created'] ?? 0) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                @svg('heroicon-o-chat-bubble-oval-left-ellipsis', 'w-5 h-5 text-gray-400')
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($data['comments_created'] ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans_choice('public-user.quick-stats-comments', $data['comments_created'] ?? 0) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                @svg('heroicon-o-hand-thumb-up', 'w-5 h-5 text-gray-400')
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($data['votes_created'] ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans_choice('public-user.quick-stats-votes', $data['votes_created'] ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="recent_activity">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ trans('public-user.recent-activity') }}
                </h3>
            </div>

            <div class="p-6">
                @if(isset($data['activities']) && count($data['activities']) > 0)
                    <div class="relative">
                        @foreach($data['activities'] as $index => $activity)
                            <div class="relative flex gap-4 {{ $loop->last ? '' : 'pb-8' }}">
                                @if(!$loop->last)
                                    <div class="absolute left-4 top-8 bottom-0 w-0.5 border-l-2 border-dashed border-gray-200 dark:border-gray-700"></div>
                                @endif

                                <div class="relative flex-shrink-0">
                                    <div class="relative flex items-center justify-center shrink-0 w-8 h-8 border rounded-full
                                    {{ $activity['type'] === 'item' ? 'text-gray-400 border border-gray-200 bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800' : '' }}
                                    {{ $activity['type'] === 'comment' ? 'text-gray-400 border border-gray-200 bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800' : '' }}
                                    {{ $activity['type'] === 'vote' ? 'text-gray-400 border border-gray-200 bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800' : '' }}">
                                        @if($activity['type'] === 'item')
                                            @svg('heroicon-o-queue-list', 'w-5 h-5')
                                        @elseif($activity['type'] === 'comment')
                                            @svg('heroicon-o-chat-bubble-oval-left-ellipsis', 'w-5 h-5')
                                        @elseif($activity['type'] === 'vote')
                                            @svg('heroicon-o-hand-thumb-up', 'w-5 h-5')
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 pt-0.5">
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                @if($activity['type'] === 'item')
                                                    {{ trans('public-user.activity-created-item') }}
                                                @elseif($activity['type'] === 'comment')
                                                    {{ trans('public-user.activity-commented') }}
                                                @elseif($activity['type'] === 'vote')
                                                    {{ trans('public-user.activity-voted') }}
                                                @endif
                                            </h4>
                                            <time
                                                    x-data="{ tooltip: '{{ $activity['created_at']->isoFormat('L LTS') }}' }"
                                                    x-tooltip="tooltip"
                                                    class="shrink-0 text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                @svg('heroicon-o-clock', 'w-3.5 h-3.5 inline -mt-1')
                                                {{ $activity['created_at']->diffForHumans() }}
                                            </time>
                                        </div>

                                        @if(isset($activity['title']))
                                            <a href="{{ $activity['url'] }}"
                                               class="text-sm font-medium text-gray-900 dark:text-gray-400 hover:underline block truncate">
                                                {{ $activity['title'] }}
                                            </a>
                                        @endif

                                        @if(isset($activity['content']))
                                            <div class="mt-2 bg-white dark:bg-gray-800 rounded-md p-3 border border-gray-200 dark:border-gray-700">
                                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 break-words">
                                                    {{ $activity['content'] }}
                                                </p>
                                            </div>
                                        @endif

                                        @if(isset($activity['project']))
                                            <div class="mt-2 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                @svg('heroicon-o-folder', 'w-3 h-3')
                                                <span class="truncate">{{ $activity['project'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                            @svg('heroicon-o-clock', 'w-8 h-8 text-gray-400 dark:text-gray-500')
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trans('public-user.no-recent-activity') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app>
