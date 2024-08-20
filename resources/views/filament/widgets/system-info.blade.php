<x-filament::widget class="space-y-4">
    <p class="text-gray-500 text-sm mb-4 dark:text-gray-400">
        {{ trans('system.description') }}
    </p>

    <x-filament::card>
        <dl class="md:grid md:grid-cols-4">
            <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 p-6 text-center md:border-0 md:border-r">
                <dt class="order-2 mt-2 text-md leading-6 font-medium text-gray-500">{{ trans('system.current-version') }}</dt>
                <dd class="order-1 text-2xl font-extrabold text-primary-600">{{ $version['currentVersion'] }}</dd>
            </div>
            <div
                class="flex flex-col border-t border-b border-gray-100 dark:border-gray-700 p-6 text-center md:border-0 md:border-r">
                <dt class="order-2 mt-2 text-md leading-6 font-medium text-gray-500">{{ trans('system.remote-version') }}</dt>
                <dd class="order-1 text-2xl font-extrabold text-primary-600">{{ $version['remoteVersion'] }}</dd>
            </div>
            <div class="flex flex-col border-t border-gray-100 p-6 text-center md:border-0">
                <dt class="order-2 mt-2 text-md leading-6 font-medium text-gray-500">
                    @if($isOutOfDate)
                        <a class="border-b border-dotted border-gray-500"
                           href="https://github.com/ploi/roadmap/releases/tag/{{ $version['remoteVersion'] }}"
                           target="_blank">
                            {{ trans( 'system.update-available') }}
                        </a>
                    @else
                        {{ trans('system.up-to-date') }}
                    @endif
                </dt>
                <dd class="order-1 text-2xl font-extrabold flex justify-center">
                    @if($isOutOfDate)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </dd>
            </div>

            <div class="flex flex-col border-t border-gray-100 dark:border-gray-700 p-6 text-center md:border-0 md:border-l">
                <dt class="order-2 mt-2 text-md leading-6 font-medium text-gray-500">{{ trans('system.php-version') }}</dt>
                <dd class="order-1 text-2xl font-extrabold text-primary-600">{{ $phpVersion }}</dd>
            </div>
        </dl>
    </x-filament::card>
</x-filament::widget>
