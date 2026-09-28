<div class="flex items-center gap-2">
    @php($likesLabel = trans_choice('changelog.votes.total-likes', $votesCount, ['likes' => $votesCount]))

    <button type="button"
            wire:click="vote"
            x-data
            x-tooltip.raw="{{ auth()->check() ? $likesLabel : trans('changelog.votes.login') }}"
            aria-label="{{ $likesLabel }}"
            aria-pressed="{{ $hasVoted ? 'true' : 'false' }}"
            @class([
                'inline-flex items-center gap-1.5 h-7 px-2.5 rounded-full text-xs font-medium ring-1 transition',
                'bg-brand-50 text-brand-700 ring-brand-200 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-300 dark:ring-brand-500/30' => $hasVoted,
                'text-gray-600 ring-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800' => ! $hasVoted,
            ])>
        @if($hasVoted)
            <x-heroicon-s-hand-thumb-up class="w-4 h-4"/>
        @else
            <x-heroicon-o-hand-thumb-up class="w-4 h-4"/>
        @endif
        <span>{{ $votesCount }}</span>
    </button>

    @if($recentVoters->isNotEmpty())
        <div class="flex -space-x-1.5">
            @foreach($recentVoters as $voter)
                <a href="{{ route('public-user', $voter['username']) }}">
                    <img src="{{ $voter['avatar'] }}"
                         class="inline object-cover w-6 h-6 border-2 border-white rounded-full dark:border-gray-800"
                         alt="{{ $voter['name'] }}" x-data x-tooltip.raw="{{ $voter['name'] }}">
                </a>
            @endforeach
        </div>

        @if($votesCount > $votersToShow)
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">+{{ $votesCount - $votersToShow }}</span>
        @endif
    @endif
</div>
