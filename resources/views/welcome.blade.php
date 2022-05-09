<x-app :breadcrumbs="[
    ['title' => 'Dashboard', 'url' => route('home')]
]">
    @if($text = app(\App\Settings\GeneralSettings::class)->welcome_text)
        <div class="prose mb-4">{!! $text !!}</div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @if(in_array('recent-items', app(\App\Settings\GeneralSettings::class)->dashboard_items))
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">Recent items</h2>

                <livewire:welcome.recent-items/>
            </div>
        @endif
        @if(in_array('recent-comments', app(\App\Settings\GeneralSettings::class)->dashboard_items))
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">Recent comments</h2>

                <livewire:welcome.recent-comments/>
            </div>
        @endif
    </div>
</x-app>
