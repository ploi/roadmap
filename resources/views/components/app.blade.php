<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Welcome') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family={{ $fontFamily['urlValue'] }}" rel="stylesheet" />

    <style>
        body {
            font-family: '{{ $fontFamily['cssValue'] }}', sans-serif;
        }
    </style>

    @if(file_exists($favIcon = storage_path('app/public/favicon.png')))
        <link href="{{ asset('storage/favicon.png') }}?v={{ md5_file($favIcon) }}" rel="icon" type="image/x-icon"/>
    @endif

    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')

    @include('partials.meta')

    {!! $brandColors !!}
    {!! $primaryColors !!}

    @if($blockRobots)
        <meta name="robots" content="noindex">
    @endif
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-950 dark:text-white">
@if($userNeedsToVerify)
    <div class="relative bg-brand-600">
        <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
            <div class="pr-16 sm:text-center sm:px-16">
                <p class="font-medium text-white">
                    <span class="md:inline">{{ trans('auth.verify-email-helper-text') }}</span>
                    <span class="block sm:ml-2 sm:inline-block">
          <a href="{{ route('verification.notice') }}" class="text-white font-bold underline"> {{ trans('auth.verify') }} <span
                  aria-hidden="true">&rarr;</span></a>
        </span>
                </p>
            </div>
        </div>
    </div>
@endif

<livewire:header :logo="$logo" />

<div class="w-full mx-auto py-5 md:space-x-10 h-full grid grid-cols-6 px-2 sm:px-6 md:px-8 max-w-[1500px]">
    @include('partials.navbar')

    <main class="flex-1 h-full col-span-6 lg:col-span-5 lg:border-l lg:pl-5">
        <div class="pb-4">
            <ul class="flex items-center space-x-0.5 text-sm font-medium text-gray-600">
                @foreach(array_filter($breadcrumbs) as $breadcrumb)
                    @if(!$loop->first)
                        <li>
                            <svg class="text-gray-400 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
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

@vite('resources/js/app.js')
@livewireScriptConfig
@filamentScripts
@livewire('notifications')

@stack('javascript')
<x-impersonate::banner/>
{!! app(\App\Settings\GeneralSettings::class)->custom_scripts !!}
</body>
</html>
