<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('project.show', $project->id)],
    ['title' => $board->title, 'url' => ''],
    ['title' => $item->title, 'url' => route('item.show', [$project->id, $item->id])]
]">
    <main class="p-4 overflow-y-scroll flex justify-center">
        <div class="block p-2 space-y-2 bg-white shadow rounded-xl lg:min-w-[60rem]">
            <header class="flex items-center px-4 py-2 space-x-4">
                <div class="flex items-center flex-1 space-x-3 overflow-hidden">
                    <div class="relative flex-shrink-0 w-10 h-10 rounded-full">
                        <div
                            class="absolute bottom-0 right-0 z-10 w-2.5 h-2.5 bg-green-500 rounded-full ring-2 ring-white">
                        </div>

                        <div class="absolute inset-0 bg-gray-200 rounded-full animate-pulse"></div>

                        <img class="absolute inset-0 object-cover rounded-full"
                             src="https://thispersondoesnotexist.com/image"
                             alt="">
                    </div>

                    <div class="overflow-hidden font-medium">
                        <p>Amparo Grimes</p>
                        <p class="text-sm text-gray-600 truncate">To: nina.pfannerstill@vandervort.net</p>
                    </div>
                </div>

                <time class="flex-shrink-0 text-sm font-medium text-gray-500">
                    12:41
                </time>
            </header>

            <div class="border-t"></div>

            <div class="p-4 prose">
                <h2>{{ $item->title }}</h2>

                {!! str($item->content)->markdown() !!}
            </div>
        </div>

    </main>
</x-app>
