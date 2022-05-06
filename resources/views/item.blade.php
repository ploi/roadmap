<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project->id)],
    ['title' => $board->title, 'url' => route('projects.boards.show', [$project->id, $board->id])],
    ['title' => $item->title, 'url' => route('projects.items.show', [$project->id, $item->id])]
]">
    <main class="p-4 overflow-y-scroll">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 space-y-4">
                <x-card class="bg-gray-50">
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

                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="block p-2 overflow-hidden transition">
                            <header class="flex items-center px-4 py-2 space-x-2">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <div class="relative flex-shrink-0 w-10 h-10 rounded-full">
                                        <img class="absolute inset-0 object-cover rounded-full"
                                             src="{{ $comment->user->getGravatar() }}"
                                             alt="">
                                    </div>

                                    <div class="overflow-hidden font-medium flex items-center space-x-2">
                                        <p>{{ $comment->user->name }}</p>
                                        @if($comment->user->is($user))
                                            <span
                                                class="inline-flex items-center justify-center h-5 px-2 text-xs font-semibold tracking-tight text-blue-700 rounded-full bg-blue-500/10">
                                            Item author
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <span>&centerdot;</span>

                                <time class="flex-shrink-0 text-xs font-medium items-center text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </time>
                            </header>

                            <div class="p-4 prose">
                                {!! str($comment->content)->markdown() !!}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="lg:col-span-1">
                <x-card class="space-y-4 bg-gray-50">
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
    </main>
</x-app>
