<li class="pb-5 pt-5 first:pt-0">
    <div class="inline-block flex space-x-3">
        <div class="flex flex-col text-center -space-y-1">
            <button wire:click="toggleUpvote">
                <x-heroicon-o-chevron-up class="w-5 h-5" />
            </button>

            <span class="">{{ $item->total_votes }}</span>
        </div>

        <a href="{{ route('projects.items.show', [$project->id, $item->id]) }}" class="flex-1">
            <p class="font-bold text-lg">{{ $item->title }}</p>
            <p>{{ $item->excerpt }}</p>
        </a>

        <div>
            {{ $comments }} {{ trans_choice('messages.comments', $comments) }}
        </div>
    </div>
</li>
