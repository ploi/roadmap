@section('title', trans('general.activity'))

<x-app :breadcrumbs="[
    ['title' => trans('general.activity'), 'url' => route('activity')]
]">
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4">
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">{{ trans('general.activity') }}</h2>
                <p class="text-gray-500 text-sm">{{ trans('general.activity-description') }}</p>
                <livewire:activity />
            </div>
        </div>
    </div>
</x-app>
