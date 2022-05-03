<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project->id)],
    ['title' => $board->title, 'url' => '']
]">
    <main class="p-4 overflow-x-auto h-full flex space-x-5">
        <section class="flex-1">
            <ul class="w-full divide-y">
                @foreach($board->items as $item)
                    <livewire:board.item-card :item="$item" />
                @endforeach
            </ul>
        </section>

        <section class="w-96 sticky top-0">
            <div class="bg-white rounded-lg shadow p-5">
                <livewire:create-item-card :board="$board" />
            </div>
        </section>

    </main>
</x-app>
