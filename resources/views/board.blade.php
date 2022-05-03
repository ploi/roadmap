<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project->id)],
    ['title' => $board->title, 'url' => '']
]">
    <main class="p-4 overflow-x-auto h-full flex space-x-5">
        <section class="flex-1">
            <ul class="w-full divide-y">
                @foreach($board->items as $item)
                    <li class="pb-5 pt-5 first:pt-0">
                        <a href="{{ route('projects.items.show', [$project->id, $item->id]) }}" class="inline-block flex space-x-3">
                            <div>
                                <span class="">5</span>
                            </div>

                            <div class="flex-1">
                                <p class="font-bold text-lg">{{ $item->title }}</p>
                                <p>{{ $item->excerpt }}</p>
                            </div>

                            <div>
                                ... comments
                            </div>
                        </a>
                    </li>
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
