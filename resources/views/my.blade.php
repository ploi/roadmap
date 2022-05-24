@section('title', 'My items')

<x-app :breadcrumbs="[
    ['title' => 'My items', 'url' => route('my')]
]">
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4">
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">Created items</h2>
                <p class="text-gray-500 text-sm">These are the items you've created.</p>
                <livewire:my />
            </div>
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">Voted items</h2>
                <p class="text-gray-500 text-sm">These are items you've voted on.</p>
                <livewire:my type="voted" />
            </div>
        </div>

        <div>
            <div class="space-y-2">
                <h2 class="text-lg tracking-tight font-bold">Recent mentions</h2>
                <p class="text-gray-500 text-sm">These are items that you've been mentioned in, click on the comment to see it.</p>
                <livewire:recent-mentions />
            </div>
        </div>
    </div>
</x-app>
