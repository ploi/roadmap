@section('title', 'Profile')

<x-app :breadcrumbs="[
    ['title' => 'Profile', 'url' => route('profile')]
]">
    <livewire:profile />
</x-app>
