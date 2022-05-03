<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project->id)]
]">
    <main class="p-4 overflow-x-auto h-full">
        <div class="inline-flex h-full min-w-full gap-4 flex-nowrap justify-center overflow-hidden">
            @forelse($boards as $board)
                <section class="h-full">
                    <div class="bg-gray-100 rounded-xl min-w-[18rem] lg:min-w-[24rem] flex flex-col max-h-full">
                        <p
                            class="p-2 font-semibold tracking-tight text-center text-gray-500 border-b bg-gray-100/80 rounded-t-xl backdrop-blur-xl backdrop-saturate-150">
                            {{ $board->title }}
                        </p>

                        <ul class="p-2 space-y-2 overflow-y-scroll flex-1">
                            @foreach($board->items as $item)
                                <li>
                                    <a href="{{ route('projects.items.show', [$project->id, $item->id]) }}" class="block p-4 space-y-4 bg-white shadow rounded-xl hover:bg-gray-50">
                                        <p>
                                            {{ $item->title }}
                                        </p>

                                        <footer class="flex items-end justify-between">
                                                    <span
                                                        class="inline-flex items-center justify-center h-6 px-2 text-sm font-semibold tracking-tight text-green-700 rounded-full bg-green-50">
                                                        {{ $item->created_at->format('d F') }}
                                                    </span>

                                            <div class="flex items-center -space-x-4">
                                                <div class="relative w-8 h-8 rounded-full ring-2 ring-white">
                                                    <div
                                                        class="absolute inset-0 bg-gray-200 rounded-full animate-pulse">
                                                    </div>

                                                    <img class="absolute inset-0 object-cover rounded-full"
                                                         src="https://thispersondoesnotexist.com/image"
                                                         alt="">
                                                </div>

                                                <div class="relative w-8 h-8 rounded-full ring-2 ring-white">
                                                    <div
                                                        class="absolute inset-0 bg-gray-200 rounded-full animate-pulse">
                                                    </div>

                                                    <img class="absolute inset-0 object-cover rounded-full"
                                                         src="https://thispersondoesnotexist.com/image"
                                                         alt="">
                                                </div>

                                                <div class="relative w-8 h-8 rounded-full ring-2 ring-white">
                                                    <div
                                                        class="absolute inset-0 bg-gray-200 rounded-full animate-pulse">
                                                    </div>

                                                    <img class="absolute inset-0 object-cover rounded-full"
                                                         src="https://thispersondoesnotexist.com/image"
                                                         alt="">
                                                </div>
                                            </div>
                                        </footer>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @empty
                <div
                    class="flex flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center border rounded-2xl">
                    <div class="flex items-center justify-center w-16 h-16 text-blue-500 bg-white rounded-full shadow">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                        </svg>
                    </div>

                    <header class="max-w-xs space-y-1">
                        <h2 class="text-xl font-semibold tracking-tight">You're all caught up</h2>

                        <p class="font-medium text-gray-500">
                            There are no boards in this project. If you're an administrator, you can add new boards via the administration area.
                        </p>
                    </header>
                </div>

            @endforelse
        </div>
    </main>
</x-app>
