<div>
    @foreach($comments[0] ?? [] as $comment)
        <x-comment :comments="$comments" :comment="$comment" :item="$item" :reply="$reply"></x-comment>
    @endforeach

    @if($reply === null && !$item->board?->block_comments)
        <form wire:submit.prevent="submit" class="space-y-4 mt-4">
            {{ $this->form }}

            <x-filament::button wire:click="submit">
                {{ trans('comments.submit') }}
            </x-filament::button>
        </form>
    @endif
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
