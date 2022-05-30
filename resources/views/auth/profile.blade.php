@section('title', trans('auth.profile'))

<x-app :breadcrumbs="[
    ['title' => trans('auth.profile'), 'url' => route('profile')]
]">
    <livewire:profile />
</x-app>
