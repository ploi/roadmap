<div class="space-y-4">
    <div class="space-y-2">
        <h1 class="font-bold text-2xl hover:text-brand-500">
            <a href="{{ route('changelog.show', $changelog) }}">{{ $changelog->title }}</a>
        </h1>

        @if(app(App\Settings\GeneralSettings::class)->show_changelog_author)
            <div class="flex items-center gap-2">
                <div class="relative w-5 h-5 rounded-full">
                    <img class="absolute inset-0 object-cover rounded-full"
                         src="{{ $changelog->user->getGravatar() }}"
                         alt="{{ $changelog->user->name }}">
                </div>
                <span class="text-xs text-gray-500">
                    {{ $changelog->user->name }} {{ trans('notifications.on') }} {{ $changelog->published_at->isoFormat('L') }}
                </span>
            </div>
        @else
            <span class="text-xs text-gray-500">
                {{ $changelog->published_at->isoFormat('L') }}
            </span>
        @endif
    </div>

    <div class="prose break-words">
        {!! str($changelog->content)->markdown() !!}
    </div>

    @if(app(App\Settings\GeneralSettings::class)->show_changelog_related_items && $changelog->items->count())
        <div class="border-t border-gray-200 w-full py-2">
            <p class="font-semibold mb-2">{{ trans('changelog.included-items') }}</p>
            <div class="grid grid-cols-4 gap-x-4 gap-y-2">
                @foreach($changelog->items as $item)
                    <a title="{{ $item->title }}"
                       href="{{ route('items.show', $item) }}"
                        class="w-full flex items-center h-6 px-2 text-sm font-semibold tracking-tight text-primary-800 rounded-md bg-primary-500/5 hover:bg-primary-700/10 shadow hover:scale-[1.015]">
                        <span class="no-underline truncate">{{ $item->title }} {{ $item->project ? '(' . $item->project->title . ')' : '' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
