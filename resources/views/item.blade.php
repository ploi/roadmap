<x-app :breadcrumbs="$project ? [
    ['title' => $project->title, 'url' => route('projects.show', $project->id)],
    ['title' => $board->title, 'url' => route('projects.boards.show', [$project->id, $board->id])],
    ['title' => $item->title, 'url' => route('projects.items.show', [$project->id, $item->id])]
]: [
['title' => 'Dashboard', 'url' => route('home')],
['title' => $item->title, 'url' => route('items.show', $item->id)],
]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 space-y-4">
                <x-card>
                    <header class="flex items-center px-4 py-2 space-x-4">
                        <div class="flex items-center flex-1 space-x-3 overflow-hidden">
                            <div class="relative flex-shrink-0 w-10 h-10 rounded-full">

                                <img class="absolute inset-0 object-cover rounded-full"
                                     src="{{ $user->getGravatar() }}"
                                     alt="">
                            </div>

                            <div class="overflow-hidden font-medium">
                                <p>{{ $user->name }}</p>
                            </div>
                        </div>
                    </header>

                    <div class="border-t"></div>

                    <div class="p-4 prose">
                        {!! str($item->content)->markdown() !!}
                    </div>
                </x-card>

                <livewire:item.comments :item="$item" />
            </div>

            <div class="lg:col-span-1">
                <x-card class="space-y-4">
                    <header class="px-2">
                        <h2>{{ $item->title }}</h2>

                        <time class="flex-shrink-0 text-sm font-medium text-gray-500">
                            {{ $item->created_at }}
                        </time>
                    </header>

                    <div class="border-t"></div>

                    <livewire:item.vote-button :item="$item"/>
                </x-card>
            </div>

        </div>
</x-app>
