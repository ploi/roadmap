<header class="sticky top-0 z-10 w-full bg-brand-500 shadow text-white"
        x-data="{ open: false }">
    <div class="w-full px-4 mx-auto sm:px-6 md:px-8 max-w-[1500px]">
        <nav class="flex items-center justify-between h-20">
            <a class="text-2xl font-semibold tracking-tight"
               href="{{ route('home') }}">
                {{ config('app.name') }}
            </a>

            <ul class="items-center hidden space-x-3 text-sm font-medium text-gray-600 md:flex">
                <li>
                    <kbd @click="$dispatch('toggle-spotlight')" class="cursor-pointer p-1 items-center shadow justify-center rounded border border-gray-400 hover:bg-gray-200 bg-white font-semibold text-gray-900">{{ trans('general.navbar-search') }}</kbd>
                </li>
                @guest
                    <li>
                        <a class="flex items-center justify-center text-white hover:text-gray-50 focus:outline-none"
                           href="{{ route('login') }}">
                            {{ trans('auth.login') }}
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center justify-center text-white hover:text-gray-50 focus:outline-none"
                           href="{{ route('register') }}">
                            {{ trans('auth.register') }}
                        </a>
                    </li>
                @endguest

                @auth
                    @if(auth()->user()->admin)
                        <li>
                            <a class="flex items-center justify-center w-10 h-10 text-red-500 transition rounded-full hover:bg-gray-500/5 focus:bg-blue-500/10 focus:outline-none"
                               href="{{ route('filament.pages.dashboard') }}">
                                <x-heroicon-o-cog class="w-7 h-7 text-white"/>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile') }}">
                            <div class="relative w-7 h-7 rounded-full">
                                <div class="absolute inset-0 bg-gray-200 rounded-full animate-pulse"></div>

                                <img class="absolute inset-0 object-cover rounded-full"
                                     src="{{ auth()->user()->getGravatar() }}"
                                     alt="{{ auth()->user()->name }}">
                            </div>
                        </a>
                    </li>
                @endauth

                    <li>
                        <x-filament::button color="secondary" onclick="Livewire.emit('openModal', 'modals.item.create-item-modal')"
                                            icon="heroicon-o-plus-circle">
                            {{ trans('items.create') }}
                        </x-filament::button>
                    </li>
            </ul>

            <!-- Hamburger -->
            <div class="md:hidden">
                <button
                    class="text-white flex items-center justify-center w-10 h-10 -mr-2 transition rounded-full focus:outline-none"
                    x-on:click="open = !open"
                    type="button">
                    <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4.75 5.75H19.25"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4.75 18.25H19.25"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4.75 12H19.25"/>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile menu -->
        <nav class="-mx-2 md:hidden"
             x-show="open"
             x-cloak>
            <div class="border-t border-brand-400"></div>

            <ul class="flex flex-col py-2 space-y-1 text-sm font-medium text-white">
                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                       href="{{ route('home') }}">
                        {{ trans('general.dashboard') }}
                    </a>
                </li>

                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                       href="{{ route('my') }}">
                        {{ trans('items.my-items') }}
                    </a>
                </li>

                <li>
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                       href="{{ route('profile') }}">
                        {{ trans('auth.profile') }}
                    </a>
                </li>
                <li>
                    <x-filament::button color="secondary" onclick="Livewire.emit('openModal', 'modals.item.create-item-modal')"
                                        icon="heroicon-o-plus-circle">
                        {{ trans('items.create') }}
                    </x-filament::button>
                </li>
            </ul>
        </nav>

        <nav class="-mx-2 md:hidden"
             x-show="open"
             x-cloak>

            <ul class="flex flex-col py-2 space-y-1 text-sm font-medium text-white">
                @foreach($projects as $project)
                    <li>
                        <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                           href="{{ route('projects.show', $project) }}">
                            {{ $project->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
