<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

    <link rel="stylesheet"
          href="{{ mix('css/app.css') }}">

    @livewireStyles
</head>
<body class="antialiased">
<div class="flex h-[720px] bg-gray-100"
     x-data="{ open: false }">

    <!-- Mobile backdrop -->
    <button class="fixed inset-0 z-20 w-full h-full bg-black/50 focus:outline-none lg:hidden"
            x-on:click="open = false"
            x-show="open"
            x-transition:enter="transition ease duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            type="button"
            title="Close sidebar"></button>

    <!-- Show and hide sidebar on mobile by switching classes: open ? 'translate-x-0' : '-translate-x-full' -->
    <aside
        class="fixed inset-y-0 left-0 z-20 flex flex-col h-screen overflow-hidden transition duration-300 bg-gray-50 lg:border-r w-72 lg:z-0 lg:translate-x-0"
        x-bind:class="open ? 'translate-x-0' : '-translate-x-full'">
        <header class="flex items-center flex-shrink-0 h-16 px-4 border-b">
            <p class="text-xl font-semibold tracking-tight">{{ config('app.name') }}</p>
        </header>

        <div class="flex-1 overflow-y-auto">
            <nav class="my-2 space-y-2">
                <ul class="px-2 space-y-1">
                    <li>
                        <a
                            @class([
                                    'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
                                    'text-white bg-blue-600' => request()->is('/'),
                                    'hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none' => !request()->is('/')
                                ])
                            href="{{ route('home') }}">

                            <x-heroicon-o-home class="w-5 h-5 {{ !request()->is('/') ? 'text-blue-500' : ''  }}"/>

                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a
                            @class([
        'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
        'text-white bg-blue-600' => request()->is('my'),
        'hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none' => !request()->is('my')
    ])
                            href="{{ route('my') }}">
                            <x-heroicon-o-view-boards class="w-5 h-5 {{ !request()->is('my') ? 'text-blue-500' : ''  }}"/>

                            <span class="font-medium">My items</span>
                        </a>
                    </li>

                    <li>
                        <a class="flex items-center h-10 px-2 space-x-2 transition rounded-lg hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none"
                           href="#">
                            <x-heroicon-o-user class="w-5 h-5 {{ !request()->is('profile') ? 'text-blue-500' : ''  }}"/>

                            <span class="font-medium">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav class="my-4 space-y-2">
                <p class="px-4 text-lg font-semibold">Projects</p>

                <ul class="px-2 space-y-1">
                    @foreach($projects as $project)
                        <li>
                            <a @class([
                                    'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
                                    'text-white bg-blue-600' => (int)request()->segment(2) === $project->id,
                                    'hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none' => (int)request()->segment(2) !== $project->id
                                ])

                               href="{{ route('projects.show', $project->id) }}">
                                <x-heroicon-o-hashtag class="w-5 h-5 {{ request()->segment(2) == $project->id ? '' : 'text-blue-500'  }}"/>

                                <span class="font-medium">{{ $project->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </aside>

    <div class="flex flex-1 w-full h-screen bg-white lg:pl-72">
        <div class="flex flex-col flex-1 overflow-hidden bg-white">
            <header
                class="sticky top-0 z-10 flex items-center justify-between flex-shrink-0 h-16 px-4 bg-white border-b">
                <aside class="flex items-center">
                    <button
                        class="flex items-center justify-center w-10 h-10 mr-2 -ml-2 text-blue-500 transition rounded-full lg:hidden hover:bg-gray-500/5 focus:bg-blue-500/10 focus:outline-none"
                        x-on:click="open = !open"
                        type="button">
                        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5" d="M4.75 5.75H19.25"/>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5" d="M4.75 18.25H19.25"/>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5" d="M4.75 12H19.25"/>
                        </svg>
                    </button>

                    <ul class="flex items-center -space-x-1 text-sm font-medium text-gray-600">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if(!$loop->first)
                                <li>
                                    <svg class="text-gray-400 w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5" d="M10.75 8.75L14.25 12L10.75 15.25"/>
                                    </svg>
                                </li>
                            @endif

                            <li>
                                <a class="transition hover:underline focus:outline-none focus:text-gray-800 focus:underline"
                                   href="{{ $breadcrumb['url'] }}">
                                    {{ $breadcrumb['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>


                <ul class="flex items-center space-x-4">
                    <li>
                        <a class="flex items-center justify-center w-10 h-10 text-blue-500 transition rounded-full hover:bg-gray-500/5 focus:bg-blue-500/10 focus:outline-none"
                           href="#">
                            <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M19.25 4.75H4.75L9.31174 10.4522C9.59544 10.8068 9.75 11.2474 9.75 11.7016V18.25C9.75 18.8023 10.1977 19.25 10.75 19.25H13.25C13.8023 19.25 14.25 18.8023 14.25 18.25V11.7016C14.25 11.2474 14.4046 10.8068 14.6883 10.4522L19.25 4.75Z"/>
                            </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
                                    </svg>
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
            </header>

            <div class="bg-white h-full overflow-y-scroll">
                {{ $slot }}
            </div>

        </div>
    </div>

    <x-filament::notification-manager/>
</div>
@livewireScripts
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
