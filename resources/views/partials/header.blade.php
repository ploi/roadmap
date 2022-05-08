 <header class="sticky top-0 z-10 w-full bg-primary shadow text-white"
            x-data="{ open: false }">
    <div class="w-full px-4 mx-auto sm:px-6 md:px-8 max-w-[1500px]">
        <nav class="flex items-center justify-between h-20">
            <a class="text-3xl font-semibold tracking-tight"
               href="{{ route('home') }}">
                {{ config('app.name') }}
            </a>

            <ul class="items-center hidden space-x-3 text-sm font-medium text-gray-600 md:flex">
                <li>
                    <x-filament::button color="secondary" onclick="Livewire.emit('openModal', 'create-item-modal')" icon="heroicon-o-plus-circle">
                        Submit item
                    </x-filament::button>
                </li>

                <li>
                    <a class="flex items-center justify-center w-10 h-10 text-blue-500 transition rounded-full hover:bg-gray-500/5 focus:bg-blue-500/10 focus:outline-none"
                       href="#">
                        <x-heroicon-o-filter class="w-7 h-7 text-white" />
                    </a>
                </li>

                @guest
                    <li>
                        <a class="flex items-center justify-center text-blue-500 hover:text-blue-600 focus:outline-none"
                           href="{{ route('login') }}">
                            Login
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center justify-center text-blue-500 hover:text-blue-600 focus:outline-none"
                           href="{{ route('register') }}">
                            Register
                        </a>
                    </li>
                @endguest

                @auth
                    @if(auth()->user()->admin)
                        <li>
                            <a class="flex items-center justify-center w-10 h-10 text-red-500 transition rounded-full hover:bg-gray-500/5 focus:bg-blue-500/10 focus:outline-none"
                               href="{{ route('filament.pages.dashboard') }}">
                                <x-heroicon-o-cog class="w-7 h-7 text-white" />
                            </a>
                        </li>
                    @endif
                    <li>
                        <div class="relative w-7 h-7 rounded-full">
                            <div class="absolute inset-0 bg-gray-200 rounded-full animate-pulse"></div>

                            <img class="absolute inset-0 object-cover rounded-full"
                                 src="{{ auth()->user()->getGravatar() }}"
                                 alt="">
                        </div>
                    </li>
                @endauth
            </ul>

            <!-- Hamburger -->
            <div class="md:hidden">
                <button
                        class="text-white flex items-center justify-center w-10 h-10 -mr-2 transition rounded-full focus:outline-none"
                        x-on:click="open = !open"
                        type="button">
                    <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 5.75H19.25"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 18.25H19.25"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 12H19.25"/>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile menu -->
        <nav class="-mx-2 md:hidden"
             x-show="open"
             x-cloak>
            <div class="border-t border-primary-400"></div>

            <ul class="flex flex-col py-2 space-y-1 text-sm font-medium text-white">
                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-primary-400"
                       href="{{ route('home') }}">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-primary-400"
                       href="{{ route('my') }}">My items</a>
                </li>

                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-primary-400"
                       href="#">Settings</a>
                </li>
            </ul>
        </nav>

        <div class="border-t border-primary-400"></div>

        <nav class="-mx-2 md:hidden"
             x-show="open"
             x-cloak>

            <ul class="flex flex-col py-2 space-y-1 text-sm font-medium text-white">
                @foreach($projects as $project)
                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-primary-400"
                       href="{{ route('projects.show', $project->id) }}">
                        {{ $project->title }}
                    </a>
                </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
