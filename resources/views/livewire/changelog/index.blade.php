@if($changelogs->count())
    <ul class="w-full space-y-6">
        @foreach($changelogs as $changelog)
            <li>
                <livewire:changelog.item :changelog="$changelog"/>
            </li>
        @endforeach
    </ul>
@else
    <div class="w-full">
        <div class="flex flex-col items-center justify-center max-w-md p-8 mx-auto space-y-6 text-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="flex items-center justify-center w-20 h-20 text-brand-500 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 rounded-full">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                </svg>
            </div>

            <header class="max-w-sm space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ trans('changelog.all-caught-up-title') }}</h2>

                <p class="text-base text-gray-600 dark:text-gray-400">
                    {{ trans('changelog.all-caught-up-description') }}
                </p>
            </header>
        </div>
    </div>
@endif
