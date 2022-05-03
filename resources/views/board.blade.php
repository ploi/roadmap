<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project->id)],
    ['title' => $board->title, 'url' => '']
]">
    <main class="p-4 overflow-x-auto h-full flex space-x-5">
        <section class="flex-1">
            @if($board->items->count())
                <ul class="w-full divide-y">
                    @foreach($board->items as $item)
                        <livewire:board.item-card :item="$item"/>
                    @endforeach
                </ul>

            @else
                <div class="w-full">
                    <div
                        class="flex flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center border rounded-2xl">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-blue-500 bg-white rounded-full shadow">
                            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                            </svg>
                        </div>

                        <header class="max-w-xs space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight">You're all caught up</h2>

                            <p class="font-medium text-gray-500">
                                There are no items (yet) in this board. You can create one on the right.
                            </p>
                        </header>
                    </div>
                </div>
            @endif
        </section>

        <section class="w-96 sticky top-0">
            <div class="bg-white rounded-lg shadow p-5">
                <livewire:create-item-card :board="$board"/>
            </div>
        </section>

    </main>
</x-app>
