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
<body class="antialiased bg-gray-50">

@include('partials.header')

<div class="w-full mx-auto py-5 md:space-x-10 h-full grid grid-cols-6 w-full px-2 sm:px-6 md:px-8 max-w-[1500px]">
    @include('partials.navbar')

    <main class="flex-1 h-full col-span-6 md:col-span-5 md:border-l md:pl-5 min-h-[600px]">
        {{ $slot }}
    </main>
</div>

<x-filament::notification-manager/>

@livewire('livewire-ui-modal')
@livewireScripts
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
