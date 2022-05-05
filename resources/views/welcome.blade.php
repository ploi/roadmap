<x-app :breadcrumbs="[
    ['title' => 'Dashboard', 'url' => route('home')]
]">
    <main class="p-4 py-10 overflow-x-auto">
        <div class="max-w-4xl mx-auto bg-gray-50 rounded-md p-5 border">
            <livewire:item.create />
        </div>
    </main>
</x-app>
