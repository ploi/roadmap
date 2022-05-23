@section('title', $user->username)

<x-app :breadcrumbs="[
    ['title' => 'Public user', 'url' => route('public-user', $user->username)],
    ['title' => $user->username, 'url' => route('public-user', $user->username)],
]">
    <p>We still need to think of a nice view for the public user pages. 🤓</p>
</x-app>
