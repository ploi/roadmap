<header class="sticky top-0 z-10 w-full bg-brand-500 shadow text-white dark:bg-gray-900 dark:border-b dark:border-white/10"
        x-data="{ open: false }"
        @keydown.window.cmd.k.prevent="$wire.mountAction('searchItem')"
        @keydown.window.ctrl.k.prevent="$wire.mountAction('searchItem')">
    <div class="w-full px-4 mx-auto sm:px-6 md:px-8 max-w-[1500px]">
        <nav class="flex items-center justify-between h-20">
            <a class="text-2xl font-semibold tracking-tight"
               href="{{ route('home') }}">
                @if(!is_null($logo) && file_exists($logoFile = storage_path('app/public/'.$logo)))
                    <img src="{{ asset('storage/'.$logo) }}?v={{ md5_file($logoFile) }}" alt="{{ config('app.name') }}"
                         class="h-8"/>
                @else
                    {{ config('app.name') }}
                @endif
            </a>

            <ul class="items-center hidden space-x-2 text-sm font-medium lg:flex">
                <li>
                    {{ $this->searchItemAction }}
                </li>

                @guest
                    <li>
                        <a class="inline-flex items-center gap-2 px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-white/20"
                           href="{{ route('login') }}">
                            {{ trans('auth.login') }}
                        </a>
                    </li>
                    @if(! app(App\Settings\GeneralSettings::class)->disable_user_registration)
                        <li>
                            <a class="inline-flex items-center gap-2 px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-white/20"
                               href="{{ route('register') }}">
                                {{ trans('auth.register') }}
                            </a>
                        </li>
                    @endif
                @endguest

                @auth
                    @if(auth()->user()->hasAdminAccess())
                        <li>
                            <a class="flex items-center justify-center w-10 h-10 transition rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
                               href="{{ \Filament\Pages\Dashboard::getUrl() }}">
                                <x-heroicon-o-cog class="w-5 h-5 text-white"/>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile') }}" class="flex items-center justify-center w-10 h-10 transition rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20">
                            <div class="relative w-8 h-8 rounded-full ring-2 ring-white/20">
                                <div class="absolute inset-0 bg-gray-200 rounded-full animate-pulse"></div>
                                <img class="absolute inset-0 object-cover rounded-full"
                                     src="{{ auth()->user()->getGravatar() }}"
                                     alt="{{ auth()->user()->name }}">
                            </div>
                        </a>
                    </li>
                @endauth

                <li class="pl-1">
                    {{ $this->submitItemAction }}
                </li>

                @if(app(\App\Settings\ColorSettings::class)->darkmode)
                    <li>
                        <x-theme-toggle/>
                    </li>
                @endif
            </ul>

            <!-- Hamburger -->
            <div class="lg:hidden">
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
        <nav class="-mx-2 lg:hidden"
             x-show="open"
             x-cloak>
            <div class="border-t border-brand-400 dark:border-white/10"></div>

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
                    <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                       href="{{ route('activity') }}">
                        {{ trans('general.activity') }}
                    </a>
                </li>

                @if(app(App\Settings\GeneralSettings::class)->enable_changelog)
                    <li>
                        <a class="block p-2 transition rounded-lg focus:outline-none hover:bg-brand-500-400"
                           href="{{ route('changelog') }}">
                            {{ trans('changelog.changelog') }}
                        </a>
                    </li>
                @endif

                <li>
                    {{ $this->searchItemAction }}
                </li>

                <li>
                    {{ $this->submitItemAction }}
                </li>
            </ul>
        </nav>

        <nav class="-mx-2 lg:hidden"
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
    <x-filament-actions::modals />
</header>
