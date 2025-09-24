<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Statistics Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4">
        @foreach($statistics as $stat)
            <div class="flex items-start space-x-3">
                <div class="{{ $stat['bg_color'] }} rounded-lg p-2">
                    @svg($stat['icon'], 'w-5 h-5 ' . $stat['color'])
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($stat['value']) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $stat['label'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent Activity Section --}}
    <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ trans('general.recent-activity') }}
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ trans('general.new-items-this-week') }}</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">+{{ $recentActivity['new_items'] }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ trans('general.new-comments-this-week') }}</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">+{{ $recentActivity['new_comments'] }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ trans('general.new-votes-this-week') }}</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">+{{ $recentActivity['new_votes'] }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ trans('general.new-users-this-week') }}</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">+{{ $recentActivity['new_users'] }}</p>
            </div>
        </div>
    </div>
</div>