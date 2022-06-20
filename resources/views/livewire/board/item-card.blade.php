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
            @if($item->isPrivate())
                <svg xmlns="http://www.w3.org/2000/svg"  width="24" height="24"
                     x-data
                     x-tooltip.raw="{{ trans('items.item-private') }}"
                     class="text-gray-500 fill-gray-500">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                </svg>

                <span>&centerdot;</span>
            @endif

            @if($item->isPinned())
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     x-data
                     x-tooltip.raw="{{ trans('items.item-pinned') }}"
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
