<div class="relative w-full p-2 m-auto space-y-2 bg-white shadow rounded-xl" x-cloak>
    <div class="px-4 py-2">
        <h2 class="text-xl font-semibold tracking-tight">{{ $title }}</h2>
    </div>

    <div class="border-t"></div>

    <div class="px-4 py-2 space-y-4">
        {{ $content }}
    </div>

    <div class="border-t"></div>

    <footer class="flex items-center px-4 py-2 space-x-4">
        {{ $buttons }}
    </footer>
    {{--<div class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150">
        @if(isset($title))
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $title }}
            </h3>
        @endif
    </div>
    <div class="bg-white px-4 sm:p-6">
        <div class="space-y-6">
            {{ $content }}
        </div>
    </div>

    <div class="bg-white px-4 pb-5 sm:px-4 sm:flex">
        {{ $buttons }}
    </div>--}}
</div>