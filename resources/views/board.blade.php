@section('title', $project->title . ' - ' . $board->title)
@section('image', $board->getOgImage($board->description, 'Roadmap - Board'))
@section('description', $board->description)

<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project)],
    ['title' => $board->title, 'url' => '']
]">
    <main class="p-4 h-full flex space-x-10 mx-auto max-w-6xl">
        <section class="flex-1 max-h-full overflow-y-scroll">
            <livewire:project.items :project="$project" :board="$board"/>
        </section>

        @if($board->canUsersCreateItem())
            <section class="w-96 sticky top-0">
                <div class="bg-white rounded-lg shadow p-5">
                    <livewire:item.create :project="$project" :board="$board"/>
                </div>
            </section>
        @endif
    </main>
</x-app>
