<div>
    @foreach($comments as $comment)
        <div class="block p-2 overflow-hidden transition" id="comment-{{ $comment->id }}">
            <header class="flex justify-between">
                <div class="flex items-center px-4 py-2 space-x-2">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="relative flex-shrink-0 w-10 h-10 rounded-full">
                            <img class="absolute inset-0 object-cover rounded-full"
                                 src="{{ $comment->user->getGravatar() }}"
                                 alt="{{ $comment->user->name }}">
                        </div>

                        <div class="overflow-hidden font-medium flex items-center space-x-2">
                            <p>{{ $comment->user->name }}</p>
                            @if($comment->user_id === $item->user_id)
                                <span
                                    class="inline-flex items-center justify-center h-5 px-2 text-xs font-semibold tracking-tight text-blue-700 rounded-full bg-blue-500/10">
                                Item author
                            </span>
                            @endif
                        </div>
                    </div>

                    <span>&centerdot;</span>

                    <time
                        x-data="{ tooltip: '{{ $comment->created_at }}' }"
                        x-tooltip="tooltip"
                        class="flex-shrink-0 text-xs font-medium items-center text-gray-500">
                        {{ $comment->created_at->diffForHumans() }}
                    </time>
                </div>

                <div class="p-2">
                    <button x-data
                            x-tooltip.raw="Click to copy link to this comment"
                            x-clipboard.raw="{{ route('items.show', $item->id) .'#comment-' . $comment->id }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-500" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </button>
                </div>
            </header>

            <div class="p-4 prose">
                {!! str($comment->content)->markdown() !!}
            </div>
        </div>
    @endforeach

    <form wire:submit.prevent="submit" class="space-y-4 mt-4">
        {{ $this->form }}

        <x-filament::button wire:click="submit">
            Submit
        </x-filament::button>
    </form>
</div>

@push('javascript')
    <script>
        (function () {
            const hash = window.location.hash;

            if (hash) {
                const commentElement = document.getElementById(hash.replace('#', ''));
                commentElement.classList.add('bg-brand-50', 'rounded-lg', 'ring-1', 'ring-brand-200');
            }
        })();
    </script>
@endpush
