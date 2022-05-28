<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Welcome') - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    {!! $brandColors !!}

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @if(file_exists($favIcon = storage_path('app/public/favicon.png')))
        <link href="{{ asset('storage/favicon.png') }}?v={{ md5_file($favIcon) }}" rel="icon" type="image/x-icon"/>
    @endif

    @livewireStyles

    @include('partials.meta')

    @if($blockRobots)
        <meta name="robots" content="noindex">
    @endif
</head>
<body class="antialiased bg-gray-50">

@include('partials.header')

<div class="w-full mx-auto py-5 md:space-x-10 h-full grid grid-cols-6 w-full px-2 sm:px-6 md:px-8 max-w-[1500px]">
    @include('partials.navbar')

    <main class="flex-1 h-full col-span-6 md:col-span-5 md:border-l md:pl-5 min-h-[600px]">
        <div class="pb-4">
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
        </div>

        {{ $slot }}
    </main>
</div>

<x-filament::notification-manager/>

@livewire('livewire-ui-spotlight')
@livewire('livewire-ui-modal')

@livewireScripts
<script src="{{ mix('js/app.js') }}"></script>
@stack('javascript')
</body>
</html>
