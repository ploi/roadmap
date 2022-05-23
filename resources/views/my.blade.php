@section('title', 'My items')

<x-app :breadcrumbs="[
    ['title' => 'My items', 'url' => route('my')]
]">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="space-y-2">
            <h2 class="text-lg tracking-tight font-bold">Created items</h2>
            <livewire:my />
        </div>
        <div class="space-y-2">
            <h2 class="text-lg tracking-tight font-bold">Voted items</h2>
            <livewire:my type="voted" />
        </div>
    </div>
</x-app>
