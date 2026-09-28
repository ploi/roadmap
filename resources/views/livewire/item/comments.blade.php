<div class="space-y-4">
    @if(count($comments[0] ?? []))
        <div class="bg-white shadow rounded-xl dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($comments[0] as $comment)
                <div class="p-4">
                    <livewire:item.comment
                        :comments="$comments"
                        :comment="$comment"
                        :item="$item"
                        key="comment-{{ $comment->id }}" />
                </div>
            @endforeach
        </div>
    @endif

    @if(!$item->board?->block_comments)
        @if(auth()->check() && auth()->user()->hasVerifiedEmail())
            <form wire:submit="submit" class="bg-white shadow rounded-xl dark:bg-gray-900 overflow-hidden">
                <div @class(['p-6' => !auth()->user()->hasAdminAccess()])>
                    {{ $this->form }}
                </div>

                <footer class="flex justify-end px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <x-filament::button wire:click="submit">
                        {{ trans('comments.submit') }}
                    </x-filament::button>
                </footer>
            </form>
        @elseif(auth()->check() && !auth()->user()->hasVerifiedEmail())
            <div class="text-primary-500">
                {{ trans('comments.verify-email-to-comment') }}
            </div>
        @else
            <div class="text-primary-500 hover:text-primary-700">
                <a href="{{ route('login', ['intended' => url()->full()]) }}">{{ trans('comments.login-to-comment') }}</a>
            </div>
        @endif
    @endif
</div>

@push('javascript')
    <script>
        (function () {
            const hash = window.location.hash;

            if (hash) {
                const commentElement = document.getElementById(hash.replace('#', ''));

                if (commentElement) {
                    commentElement.classList.add('bg-brand-50', 'dark:bg-brand-900/30', 'rounded-lg', 'ring-1', 'ring-brand-200', 'dark:ring-brand-800', '-mx-3', 'px-3', '-my-2', 'py-2');
                }
            }
        })();
    </script>
@endpush
