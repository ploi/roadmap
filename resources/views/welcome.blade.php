<x-app :breadcrumbs="[
    ['title' => 'Dashboard', 'url' => route('home')]
]">
    <main class="p-4 overflow-x-auto">
       <livewire:item.create />
    </main>
</x-app>
