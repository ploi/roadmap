@section('title', 'Changelog')@show
@section('image', App\Services\OgImageGenerator::make('View changelog')->withSubject('Changelog')->withFilename('changelog.jpg')->generate()->getPublicUrl())
@section('description', 'View changelog for ' . config('app.name'))

<x-app :breadcrumbs="collect([
    ['title' => 'Changelog', 'url' => route('changelog')],
])->when(request()->routeIs('changelog.show'), fn ($collection) => $collection->push(['title' => $changelogs->first()->title, 'url' => route('changelog.show', $changelogs->first())]))->toArray()">
    <main class="p-4 h-full flex space-x-10 mx-auto max-w-6xl">
        <section class="flex-1 max-h-full overflow-y-scroll">
            <livewire:changelog.index :changelogs="$changelogs"/>
        </section>
    </main>
</x-app>
