<div class="space-y-4">
    @foreach($comments as $comment)
        <div class="block p-2 overflow-hidden transition">
            <header class="flex items-center px-4 py-2 space-x-2">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="relative flex-shrink-0 w-10 h-10 rounded-full">
                        <img class="absolute inset-0 object-cover rounded-full"
                             src="{{ $comment->user->getGravatar() }}"
                             alt="">
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

                <time class="flex-shrink-0 text-xs font-medium items-center text-gray-500">
                    {{ $comment->created_at->diffForHumans() }}
                </time>
            </header>

            <div class="p-4 prose">
                {!! str($comment->content)->markdown() !!}
            </div>
        </div>
    @endforeach

    <form wire:submit.prevent="submit" class="space-y-4">
        {{ $this->form }}

        <x-filament::button wire:click="submit">
            Submit
        </x-filament::button>
    </form>
</div>

