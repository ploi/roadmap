<x-app :breadcrumbs="[
    ['title' => 'Dashboard', 'url' => route('home')]
]">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="space-y-2">
            <h2 class="text-lg tracking-tight font-bold">Recent items</h2>

            <livewire:welcome.recent-items />
        </div>
        <div class="space-y-2">
            <h2 class="text-lg tracking-tight font-bold">Recent comments</h2>

            <livewire:welcome.recent-comments />
        </div>
    </div>
</x-app>
