@section('title', $item->title)
@section('image', $item->getOgImage('"' . $item->excerpt .'"', 'Roadmap - Item'))
@section('description', $item->excerpt)

<x-app :breadcrumbs="$project ? [
    ['title' => $project->title, 'url' => route('projects.show', $project)],
    ['title' => $board->title, 'url' => route('projects.boards.show', [$project, $board])],
    ['title' => $item->title, 'url' => route('projects.items.show', [$project, $item])]
]: [
['title' => 'Dashboard', 'url' => route('home')],
['title' => $item->title, 'url' => route('items.show', $item)],
]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <x-card>
                <header class="flex items-center px-4 py-2 space-x-4">
                    <div class="flex items-center flex-1 space-x-3 overflow-hidden">
                        @if($user)
                            <div class="relative flex-shrink-0 w-10 h-10 rounded-full">
                                <img class="absolute inset-0 object-cover rounded-full"
                                     src="{{ $user->getGravatar() }}"
                                     alt="{{ $user->name }}">
                            </div>
                        @endif

                        <div class="overflow-hidden font-medium">
                            <p>{{ $user->name ?? '-Unknown user-' }}</p>
                        </div>

                        @if($item->board)
                            <div class="flex-1">
                                <span
                                    class="float-right inline-flex items-center justify-center h-8 px-3 text-sm tracking-tight font-bold text-gray-700 border border-gray-400 rounded-lg bg-white">{{ $item->board->title }}</span>
                            </div>
                        @endif
                    </div>
                </header>

                <div class="border-t"></div>

                <div class="p-4 prose break-words">
                    {!! str($item->content)->markdown() !!}
                </div>
            </x-card>

            <livewire:item.comments :item="$item"/>
        </div>

        <div class="lg:col-span-1 space-y-4">
            <x-card class="space-y-4">
                <header class="px-2 py-2">
                    <h2>{{ $item->title }}</h2>

                    <time class="flex-shrink-0 text-sm font-medium text-gray-500">
                        {{ $item->created_at->isoFormat('L LTS') }}
                    </time>

                    @if(app(\App\Settings\GeneralSettings::class)->enable_item_age)
                        <span class="text-sm font-medium text-gray-500">
                            ({{ $item->created_at->diffInDays(now()) }} {{ trans_choice('items.days-ago', $item->created_at->diffInDays(now())) }})
                        </span>
                    @endif
                </header>

                <div class="border-t"></div>

                <livewire:item.vote-button :item="$item"/>

                @if(auth()->check() && auth()->user()->canAccessFilament())
                    <div class="border-t mb-2"></div>

                    <a class="text-red-500 hover:text-red-700 block ml-1"
                       href="{{ route('filament.resources.items.edit', $item) }}">Administer item</a>
                @endif
            </x-card>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 h-full ml-4 border-l border-dashed"></div>

                <ul class="space-y-4">
                    @foreach($activities as $activity)
                        <li class="flex space-x-3">
                            <div
                                class="relative flex items-center justify-center flex-shrink-0 w-8 h-8 text-gray-400 border border-gray-200 rounded-full bg-gray-50">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M12 18.25C15.5 18.25 19.25 16.5 19.25 12C19.25 7.5 15.5 5.75 12 5.75C8.5 5.75 4.75 7.5 4.75 12C4.75 13.0298 4.94639 13.9156 5.29123 14.6693C5.50618 15.1392 5.62675 15.6573 5.53154 16.1651L5.26934 17.5635C5.13974 18.2547 5.74527 18.8603 6.43651 18.7307L9.64388 18.1293C9.896 18.082 10.1545 18.0861 10.4078 18.1263C10.935 18.2099 11.4704 18.25 12 18.25Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          d="M9.5 12C9.5 12.2761 9.27614 12.5 9 12.5C8.72386 12.5 8.5 12.2761 8.5 12C8.5 11.7239 8.72386 11.5 9 11.5C9.27614 11.5 9.5 11.7239 9.5 12Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          d="M12.5 12C12.5 12.2761 12.2761 12.5 12 12.5C11.7239 12.5 11.5 12.2761 11.5 12C11.5 11.7239 11.7239 11.5 12 11.5C12.2761 11.5 12.5 11.7239 12.5 12Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.5 12C15.5 12.2761 15.2761 12.5 15 12.5C14.7239 12.5 14.5 12.2761 14.5 12C14.5 11.7239 14.7239 11.5 15 11.5C15.2761 11.5 15.5 11.7239 15.5 12Z"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-medium">
                                    <span class="font-semibold">
                                        {{ $activity->causer->name ?? 'Unknown user' }}
                                    </span>
                                    {{ $activity->description }}
                                </p>

                                <span class="mt-1 text-xs font-medium text-gray-500"
                                      x-data="{ tooltip: '{{ $activity->created_at->isoFormat('L LTS') }}' }"
                                      x-tooltip="tooltip">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app>
