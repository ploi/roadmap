<x-app>
    <main class="p-4 overflow-x-auto">
        <div class="inline-flex h-full min-w-full gap-4 flex-nowrap justify-center">
            @foreach($boards as $board)
                <section class="overflow-y-auto bg-gray-100 rounded-xl min-w-[18rem] lg:min-w-[24rem]">
                    <p
                        class="sticky top-0 z-10 p-2 font-semibold tracking-tight text-center text-gray-500 border-b bg-gray-100/80 rounded-t-xl backdrop-blur-xl backdrop-saturate-150">
                        {{ $board->title }}
                    </p>

                    <ul class="p-2 space-y-2">
                        @foreach($board->items as $item)
                        <li>
                            <a href="#" class="block p-4 space-y-4 bg-white shadow rounded-xl hover:bg-gray-50">
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

                        <li>
                            <a href="#"
                                class="block p-6 font-medium text-center text-gray-500 border border-gray-300 border-dashed rounded-xl">
                                <p>Add</p>
                            </a>
                        </li>
                    </ul>
                </section>
            @endforeach
        </div>
    </main>
</x-app>
