<li class="pb-5 pt-5 first:pt-0 group">
    <div class="flex space-x-3">
        <div class="flex flex-col text-center space-y-1">
            <button wire:click="toggleUpvote" class="hover:text-primary-500">
                <x-heroicon-o-chevron-up class="w-5 h-5"/>
            </button>

            <span class="">{{ $item->total_votes }}</span>
        </div>

        <a href="{{ route('projects.items.show', [$project, $item]) }}" class="flex-1">
            <p class="font-bold text-lg group-hover:text-brand-500">{{ $item->title }}</p>
            <p>{{ $item->excerpt }}</p>
        </a>

        <div class="flex space-x-2">
            @if($item->isPinned())
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     x-data
                     x-tooltip.raw="This item is pinned"
                     class="text-gray-500 fill-gray-500">
                    <path
                        d="M15 11.586V6h2V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2h2v5.586l-2.707 1.707A.996.996 0 0 0 6 14v2a1 1 0 0 0 1 1h4v3l1 2 1-2v-3h4a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L15 11.586z"></path>
                </svg>

                <span>&centerdot;</span>
            @endif

            <span>
                {{ $comments }} {{ trans_choice('messages.comments', $comments) }}
            </span>
        </div>
    </div>
</li>
