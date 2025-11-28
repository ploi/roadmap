<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Tab Navigation --}}
    <div class="flex border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <button
            wire:click="setActiveTab('voters')"
            @class([
                'flex-1 px-4 py-3 text-sm font-medium transition-colors duration-200 focus:outline-none',
                'text-brand-600 bg-white dark:text-brand-400 dark:bg-gray-800 border-b-2 border-brand-600 dark:border-brand-400' => $activeTab === 'voters',
                'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200' => $activeTab !== 'voters'
            ])
        >
            {{ trans('general.top-voters') }}
        </button>
        <button
            wire:click="setActiveTab('commenters')"
            @class([
                'flex-1 px-4 py-3 text-sm font-medium transition-colors duration-200 focus:outline-none',
                'text-brand-600 bg-white dark:text-brand-400 dark:bg-gray-800 border-b-2 border-brand-600 dark:border-brand-400' => $activeTab === 'commenters',
                'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200' => $activeTab !== 'commenters'
            ])
        >
            {{ trans('general.top-commenters') }}
        </button>
    </div>

    {{-- Leaderboard Content --}}
    <div class="px-4">
        @if($activeTab === 'voters' && $topVoters->isNotEmpty())
            <div class="space-y-2">
                @foreach($topVoters as $index => $user)
                    <div @class([
                        'flex items-center justify-between py-2',
                        'bg-yellow-50/50 dark:bg-yellow-900/10 -mx-4 px-4 rounded-lg' => $index === 0,
                        'border-b border-gray-100 dark:border-gray-700' => $index < count($topVoters) - 1
                    ])>
                        <div class="flex items-center space-x-3">
                            {{-- Rank --}}
                            <span @class([
                                'w-5 text-center',
                                'text-lg font-bold' => $index === 0,
                                'text-sm font-semibold' => $index !== 0,
                                'text-yellow-600 dark:text-yellow-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)] dark:drop-shadow-[0_0_8px_rgba(251,191,36,0.8)]' => $index === 0,
                                'text-gray-500 dark:text-gray-400' => $index === 1,
                                'text-orange-600 dark:text-orange-400' => $index === 2,
                                'text-gray-400 dark:text-gray-500' => $index > 2
                            ])>
                                {{ $index + 1 }}
                            </span>

                            {{-- User Avatar --}}
                            <a href="{{ route('public-user', $user->username) }}">
                            <img 
                                src="{{ $user->getGravatar(30) }}" 
                                alt="{{ $user->name }}"
                                class="w-7 h-7 rounded-full"
                            >
                            </a>

                            {{-- User Name --}}
                            <a class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-[150px] hover:underline ease-in-out" href="{{ route('public-user', $user->username) }}">
                                {{ $user->name }}
                            </a>
                        </div>

                        {{-- Vote Count --}}
                        <div class="flex items-center space-x-1">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $user->votes_count }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif($activeTab === 'commenters' && $topCommenters->isNotEmpty())
            <div class="space-y-2">
                @foreach($topCommenters as $index => $user)
                    <div @class([
                        'flex items-center justify-between py-2',
                        'bg-yellow-50/50 dark:bg-yellow-900/10 -mx-4 px-4 rounded-lg' => $index === 0,
                        'border-b border-gray-100 dark:border-gray-700' => $index < count($topCommenters) - 1
                    ])>
                        <div class="flex items-center space-x-3">
                            {{-- Rank --}}
                            <span @class([
                                'w-5 text-center',
                                'text-lg font-bold' => $index === 0,
                                'text-sm font-semibold' => $index !== 0,
                                'text-yellow-600 dark:text-yellow-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)] dark:drop-shadow-[0_0_8px_rgba(251,191,36,0.8)]' => $index === 0,
                                'text-gray-500 dark:text-gray-400' => $index === 1,
                                'text-orange-600 dark:text-orange-400' => $index === 2,
                                'text-gray-400 dark:text-gray-500' => $index > 2
                            ])>
                                {{ $index + 1 }}
                            </span>

                            {{-- User Avatar --}}
                            <a href="{{ route('public-user', $user->username) }}">
                            <img 
                                src="{{ $user->getGravatar(30) }}" 
                                alt="{{ $user->name }}"
                                class="w-7 h-7 rounded-full"
                            >
                            </a>

                            {{-- User Name --}}
                            <a class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-[150px] hover:underline ease-in-out" href="{{ route('public-user', $user->username) }}">
                                {{ $user->name }}
                            </a>
                        </div>

                        {{-- Comment Count --}}
                        <div class="flex items-center space-x-1">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $user->comments_count }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ trans('general.no-data-available') }}</p>
            </div>
        @endif
    </div>
</div>