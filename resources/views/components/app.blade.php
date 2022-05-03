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
        class="fixed inset-y-0 left-0 z-20 flex flex-col h-screen overflow-hidden transition duration-300 bg-gray-100 lg:border-r w-72 lg:z-0 lg:translate-x-0"
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
                            <svg
                                @class(['w-7 h-7', 'text-blue-500' => !request()->is('/')]) xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M6.75024 19.2502H17.2502C18.3548 19.2502 19.2502 18.3548 19.2502 17.2502V9.75025L12.0002 4.75024L4.75024 9.75025V17.2502C4.75024 18.3548 5.64568 19.2502 6.75024 19.2502Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M9.74963 15.7493C9.74963 14.6447 10.6451 13.7493 11.7496 13.7493H12.2496C13.3542 13.7493 14.2496 14.6447 14.2496 15.7493V19.2493H9.74963V15.7493Z"/>
                            </svg>
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
                            <svg
                                @class(['w-7 h-7', 'text-blue-500' => !request()->is('my')]) xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M5.75 19.2502H18.25C18.8023 19.2502 19.25 18.8025 19.25 18.2502V5.75C19.25 5.19772 18.8023 4.75 18.25 4.75H5.75C5.19772 4.75 4.75 5.19772 4.75 5.75V18.2502C4.75 18.8025 5.19772 19.2502 5.75 19.2502Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5" d="M9.25 5V19"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5" d="M14.75 5V19"/>
                            </svg>
                            <span class="font-medium">My items</span>
                        </a>
                    </li>

                    <li>
                        <a class="flex items-center h-10 px-2 space-x-2 transition rounded-lg hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none"
                           href="#">
                            <svg class="text-blue-500 w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M13.1191 5.61336C13.0508 5.11856 12.6279 4.75 12.1285 4.75H11.8715C11.3721 4.75 10.9492 5.11856 10.8809 5.61336L10.7938 6.24511C10.7382 6.64815 10.4403 6.96897 10.0622 7.11922C10.006 7.14156 9.95021 7.16484 9.89497 7.18905C9.52217 7.3524 9.08438 7.3384 8.75876 7.09419L8.45119 6.86351C8.05307 6.56492 7.49597 6.60451 7.14408 6.9564L6.95641 7.14408C6.60452 7.49597 6.56492 8.05306 6.86351 8.45118L7.09419 8.75876C7.33841 9.08437 7.3524 9.52216 7.18905 9.89497C7.16484 9.95021 7.14156 10.006 7.11922 10.0622C6.96897 10.4403 6.64815 10.7382 6.24511 10.7938L5.61336 10.8809C5.11856 10.9492 4.75 11.372 4.75 11.8715V12.1285C4.75 12.6279 5.11856 13.0508 5.61336 13.1191L6.24511 13.2062C6.64815 13.2618 6.96897 13.5597 7.11922 13.9378C7.14156 13.994 7.16484 14.0498 7.18905 14.105C7.3524 14.4778 7.3384 14.9156 7.09419 15.2412L6.86351 15.5488C6.56492 15.9469 6.60451 16.504 6.9564 16.8559L7.14408 17.0436C7.49597 17.3955 8.05306 17.4351 8.45118 17.1365L8.75876 16.9058C9.08437 16.6616 9.52216 16.6476 9.89496 16.811C9.95021 16.8352 10.006 16.8584 10.0622 16.8808C10.4403 17.031 10.7382 17.3519 10.7938 17.7549L10.8809 18.3866C10.9492 18.8814 11.3721 19.25 11.8715 19.25H12.1285C12.6279 19.25 13.0508 18.8814 13.1191 18.3866L13.2062 17.7549C13.2618 17.3519 13.5597 17.031 13.9378 16.8808C13.994 16.8584 14.0498 16.8352 14.105 16.8109C14.4778 16.6476 14.9156 16.6616 15.2412 16.9058L15.5488 17.1365C15.9469 17.4351 16.504 17.3955 16.8559 17.0436L17.0436 16.8559C17.3955 16.504 17.4351 15.9469 17.1365 15.5488L16.9058 15.2412C16.6616 14.9156 16.6476 14.4778 16.811 14.105C16.8352 14.0498 16.8584 13.994 16.8808 13.9378C17.031 13.5597 17.3519 13.2618 17.7549 13.2062L18.3866 13.1191C18.8814 13.0508 19.25 12.6279 19.25 12.1285V11.8715C19.25 11.3721 18.8814 10.9492 18.3866 10.8809L17.7549 10.7938C17.3519 10.7382 17.031 10.4403 16.8808 10.0622C16.8584 10.006 16.8352 9.95021 16.8109 9.89496C16.6476 9.52216 16.6616 9.08437 16.9058 8.75875L17.1365 8.4512C17.4351 8.05308 17.3955 7.49599 17.0436 7.1441L16.8559 6.95642C16.504 6.60453 15.9469 6.56494 15.5488 6.86353L15.2412 7.09419C14.9156 7.33841 14.4778 7.3524 14.105 7.18905C14.0498 7.16484 13.994 7.14156 13.9378 7.11922C13.5597 6.96897 13.2618 6.64815 13.2062 6.24511L13.1191 5.61336Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M13.25 12C13.25 12.6904 12.6904 13.25 12 13.25C11.3096 13.25 10.75 12.6904 10.75 12C10.75 11.3096 11.3096 10.75 12 10.75C12.6904 10.75 13.25 11.3096 13.25 12Z"/>
                            </svg>
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
                                    'text-white bg-blue-600' => (int)request()->route('project') === $project->id,
                                    'hover:bg-gray-500/5 focus:bg-blue-500/10 focus:text-blue-600 focus:outline-none' => (int)request()->route('project') !== $project->id
                                ])

                               href="{{ route('projects.show', $project->id) }}">
                                <svg
                                    @class(['w-7 h-7', 'text-blue-500' => (int)request()->route('project') !== $project->id])
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5" d="M10.25 4.75L7.75 19.25"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5" d="M16.25 4.75L13.75 19.25"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5" d="M19.25 8.75H5.75"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5" d="M18.25 15.25H4.75"/>
                                </svg>
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


                <ul class="flex items-center space-x-2">
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

            <div class="bg-gray-50 h-full overflow-y-scroll">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
@livewireScripts
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
