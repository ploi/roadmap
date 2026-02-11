@section('title', $project->title)@show
@section('image', $project->getOgImage($project->description, 'Roadmap - Project'))@show
@section('description', $project->description)@show
@section('canonical', route('projects.show', $project))@show

<x-app :breadcrumbs="[
    ['title' => $project->title, 'url' => route('projects.show', $project)]
]" :current-project-id="$project->id">
    <div
        @class([
        'w-full h-[calc(100vh-170px)] overflow-x-auto',
        ])
    >
        <div
            @class([
            'inline-flex h-full w-full min-w-full gap-4 flex-nowrap overflow-x-scroll',
            'justify-center' => app(\App\Settings\GeneralSettings::class)->board_centered
            ])
        >
            @forelse($boards as $board)
                <livewire:project.board-column :project="$project" :board="$board" :key="$board->id" />
            @empty
                <div class="w-full">
                    <div
                        class="flex flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center border rounded-2xl">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-blue-500 bg-white rounded-full shadow dark:bg-gray-900">
                            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                            </svg>
                        </div>

                        <header class="max-w-xs space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight">{{ trans('items.all-caught-up-title') }}</h2>

                            <p class="font-medium text-gray-500 dark:text-gray-400">
                                {{ trans('messages.no-boards') }}
                            </p>
                        </header>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app>
