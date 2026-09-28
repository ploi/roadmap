<div
    @class([
        'bg-warning-50/60 dark:bg-warning-500/5 rounded-lg ring-1 ring-warning-200 dark:ring-warning-500/20 -mx-3 px-3 -my-2 py-2' => $comment->private && !$comment->parent?->private,
        'transition',
    ])
    id="comment-{{ $comment->id }}">
    <div class="flex gap-3">
        <a href="{{ route('public-user', $comment->user->username) }}" class="shrink-0">
            <img class="w-8 h-8 object-cover rounded-full"
                 src="{{ $comment->user->getGravatar() }}"
                 alt="{{ $comment->user->name }}">
        </a>

        <div class="flex-1 min-w-0">
            <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 min-h-8">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 min-w-0 text-sm">
                    <a href="{{ route('public-user', $comment->user->username) }}"
                       class="font-medium truncate hover:underline ease-in-out">
                        {{ $comment->user->name }}
                    </a>

                    @if($comment->user_id === $item->user_id)
                        <span
                            class="inline-flex items-center py-0.5 px-2 text-xs font-semibold tracking-tight text-brand-900 rounded-full bg-brand-200">
                            {{ trans('comments.item-author') }}
                        </span>
                    @endif

                    @if($comment->private && !$comment->parent?->private)
                        <span
                            class="inline-flex items-center gap-1 py-0.5 px-2 text-xs font-medium tracking-tight text-warning-800 rounded-full bg-warning-100 dark:bg-warning-500/10 dark:text-warning-300">
                            <x-heroicon-s-lock-closed class="w-3 h-3"/>
                            {{ trans('comments.private-note') }}
                        </span>
                    @endif

                    <span class="text-gray-300 dark:text-gray-600">&centerdot;</span>

                    <time
                        x-data="{ tooltip: '{{ $comment->created_at->isoFormat('L LTS') }}' }"
                        x-tooltip="tooltip"
                        class="shrink-0 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $comment->created_at->diffForHumans() }}
                    </time>

                    @if($comment->created_at != $comment->updated_at)
                        <span class="text-gray-300 dark:text-gray-600">&centerdot;</span>

                        <span
                            x-data="{ tooltip: '{{ $comment->updated_at->isoFormat('L LTS') }}' }"
                            x-tooltip="tooltip"
                            class="text-xs font-medium text-gray-400 dark:text-gray-500">
                            {{ trans('comments.edited') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-gray-300 dark:text-gray-600">
                    @if($comment->user->is(auth()->user()))
                        {{ ($this->editAction)(['comment' => $comment->id]) }}
                        <span>&centerdot;</span>
                    @endif
                    @if(!$item->board?->block_comments)
                        {{ $this->replyAction }}
                        <span>&centerdot;</span>
                    @endif
                    @if(auth()->user()?->hasAdminAccess())
                        {{ ($this->deleteAction)(['comment' => $comment->id]) }}
                        <span>&centerdot;</span>
                    @endif
                    <button x-data
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            x-tooltip.raw="{{ trans('comments.click-to-copy') }}"
                            x-clipboard.raw="{{ route('items.show', $item) . '#comment-' . $comment->id }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </button>
                </div>
            </header>

            <div class="mt-1 prose prose-sm max-w-none break-words dark:prose-invert dark:text-gray-400">
                {!! str($comment->content)->markdown()->sanitizeHtml() !!}
            </div>

            <div class="mt-2">
                <livewire:item.vote-button :model="$comment" :hideSubscribeOption="true" :compact="true"/>
            </div>

            @if($isReplying)
                <form wire:submit="submitReply"
                      x-data
                      x-init="
                          $dispatch('comment-reply-opened', { id: {{ $comment->id }} });
                          $nextTick(() => $el.scrollIntoView({ block: 'nearest', behavior: 'smooth' }))
                      "
                      x-on:comment-reply-opened.window="if ($event.detail.id !== {{ $comment->id }}) $wire.cancelReply()"
                      @class([
                          'mt-3 rounded-xl ring-1 overflow-hidden',
                          'ring-warning-200 bg-warning-50/60 dark:ring-warning-500/20 dark:bg-warning-500/5' => $comment->private,
                          'ring-gray-200 dark:ring-gray-700' => ! $comment->private,
                      ])>
                    @if($comment->private)
                        <div class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-warning-800 border-b border-warning-200 dark:text-warning-300 dark:border-warning-500/20">
                            <x-heroicon-s-lock-closed class="w-3.5 h-3.5 shrink-0"/>
                            {{ trans('comments.private-reply-hint') }}
                        </div>
                    @endif

                    <div class="p-4">
                        {{ $this->form }}
                    </div>

                    <footer class="flex justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button color="gray" wire:click="cancelReply">
                            {{ trans('comments.cancel') }}
                        </x-filament::button>

                        <x-filament::button type="submit">
                            {{ trans('comments.reply') }}
                        </x-filament::button>
                    </footer>
                </form>
            @endif
        </div>
    </div>

    @if(count($comments[$comment->id] ?? []))
        <div class="mt-4 ml-4 pl-4 space-y-4 border-l-2 border-gray-100 dark:border-gray-800">
            @foreach($comments[$comment->id] as $replyComment)
                <livewire:item.comment
                    :comments="$comments"
                    :comment="$replyComment"
                    :item="$item"
                    key="comment-{{ $comment->id }}-{{ $replyComment->id }}"
                />
            @endforeach
        </div>
    @endif

    <x-filament-actions::modals />
</div>
