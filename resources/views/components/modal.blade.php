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
</div>
