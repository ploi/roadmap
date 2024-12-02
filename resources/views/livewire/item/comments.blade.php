<div>
    @foreach($comments[0] ?? [] as $comment)
        <livewire:item.comment
            :comments="$comments"
            :comment="$comment"
            :item="$item"
            :reply="$reply"
            key="comment-{{ $comment->id }}" />
    @endforeach

    @if($reply === null && !$item->board?->block_comments)
        <form wire:submit="submit" class="space-y-4 mt-4">
            @if(auth()->check() && auth()->user()->hasVerifiedEmail())
                {{ $this->form }}

                <x-filament::button wire:click="submit">
                    {{ trans('comments.submit') }}
                </x-filament::button>
            @elseif(auth()->check() && !auth()->user()->hasVerifiedEmail())
                <div class="text-primary-500  mt-4">
                    {{ trans('comments.verify-email-to-comment') }}
                </div>
            @else
                <div class="text-primary-500 hover:text-primary-700 mt-4">
                    <a href="{{ route('login', ['intended' => url()->full()]) }}">{{ trans('comments.login-to-comment') }}</a>
                </div>
            @endif
        </form>
    @endif
</div>

@push('javascript')
    <script>
        (function () {
            const hash = window.location.hash;

            if (hash) {
                const commentElement = document.getElementById(hash.replace('#', ''));
                commentElement.classList.add('bg-brand-50', 'rounded-lg', 'ring-1', 'ring-brand-200', 'mt-2', 'mb-2');
            }
        })();
    </script>
@endpush
