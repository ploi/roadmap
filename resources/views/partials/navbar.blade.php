<aside class="col-span-1 hidden md:block">
    <nav class="my-2 space-y-2">
        <ul class="space-y-1">
            <li>
                <a
                    @class([
                            'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
                            'text-white bg-primary-500' => request()->is('/'),
                            'hover:bg-gray-500/5 focus:bg-primary-500/10 focus:text-primary-600 focus:outline-none' => !request()->is('/')
                        ])
                    href="{{ route('home') }}">

                    <x-heroicon-o-home class="w-5 h-5 {{ !request()->is('/') ? 'text-primary-500' : ''  }}"/>

                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <li>
                <a
                    @class([
                        'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
                        'text-white bg-primary-500' => request()->is('my'),
                        'hover:bg-gray-500/5 focus:bg-primary-500/10 focus:text-primary-600 focus:outline-none' => !request()->is('my')
                    ])
                    href="{{ route('my') }}">
                    <x-heroicon-o-view-boards class="w-5 h-5 {{ !request()->is('my') ? 'text-primary-500' : ''  }}"/>

                    <span class="font-medium">My items</span>
                </a>
            </li>

            <li>
                <a class="flex items-center h-10 px-2 space-x-2 transition rounded-lg hover:bg-gray-500/5 focus:bg-primary-500/10 focus:text-primary-600 focus:outline-none"
                   href="#">
                    <x-heroicon-o-user class="w-5 h-5 {{ !request()->is('profile') ? 'text-primary-500' : ''  }}"/>

                    <span class="font-medium">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <nav class="my-4 space-y-2">
        <p class="px-4 text-lg font-semibold">Projects</p>

        @if($projects->count() > 0)
            <ul class="px-2 space-y-1">
                @foreach($projects as $project)
                    <li>
                        <a @class([
                                    'flex items-center h-10 px-2 space-x-2 transition rounded-lg ',
                                    'text-white bg-primary-500' => (int)request()->segment(2) === $project->id,
                                    'hover:bg-gray-500/5 focus:bg-primary-500/10 focus:text-primary-600 focus:outline-none' => (int)request()->segment(2) !== $project->id
                                ])

                           href="{{ route('projects.show', $project->id) }}">
                            <x-heroicon-o-hashtag class="w-5 h-5 {{ request()->segment(2) == $project->id ? '' : 'text-primary-500'  }}"/>

                            <span class="font-medium">{{ $project->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-4">
                <span class="text-sm text-gray-500">There are no projects yet.</span>
            </div>
        @endif
    </nav>
</aside>