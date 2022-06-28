<div class="space-y-4">
    <div class="space-y-2">
        <h1 class="font-bold text-2xl hover:text-brand-500">
            <a href="{{ route('changelog.show', $changelog) }}">{{ $changelog->title }}</a>
        </h1>

        @if(app(App\Settings\GeneralSettings::class)->show_changelog_author)
            <div class="flex items-center gap-1">
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
        <div>
            @foreach($changelog->items as $item)
                <span
                    class="inline-flex items-center justify-center h-6 px-2 text-sm font-semibold tracking-tight text-gray-600 rounded-md bg-gray-500/5 hover:bg-gray-700/10">
                        <a class="no-underline"
                           title="{{ $item->title }}"
                           href="{{ route('items.show', $item) }}">{{ $item->title }} {{ $item->project ? '(' . $item->project->title . ')' : '' }}</a>
                    </span>
            @endforeach
        </div>
    @endif
</div>
