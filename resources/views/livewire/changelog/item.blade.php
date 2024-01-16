<div class="space-y-4">
    <div class="flex flex-wrap justify-between">
        <div class="flex flex-col">
            <div class="flex flex-wrap flex-col">
                <h1 class="font-bold text-2xl">{{ $changelog->title }}</h1>

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
                    <span class="text-xs text-gray-500">{{ $changelog->published_at->isoFormat('L') }}</span>
                @endif
            </div>

            <div class="prose break-words mt-2">
                {!! str($changelog->content)->markdown() !!}
            </div>

        </div>

        @if(app(App\Settings\GeneralSettings::class)->show_changelog_like)
            <livewire:changelog.vote :changelog="$changelog"/>
        @endif
    </div>

    @if(app(App\Settings\GeneralSettings::class)->show_changelog_related_items && $changelog->items->count())
        <div class="w-full bg-gray-100 rounded-lg p-5">
            <div class="space-y-5">
                {{--@foreach($changelog->items()->->get() as $item)
                    <a title="{{ $item->title }}"
                       href="{{ route('items.show', $item) }}"
                        class="w-full flex items-center h-6 px-2 text-sm font-semibold tracking-tight text-primary-800 rounded-md bg-primary-500/5 hover:bg-primary-700/10 shadow hover:scale-[1.015]">
                        <span class="no-underline truncate">{{ $item->title }} {{ $item->project ? '(' . $item->project->title . ')' : '' }}</span>
                    </a>
                @endforeach--}}
                @php
                    $tags = \App\Models\Tag::query()
                        ->forChangelog($changelog)
                        ->get();

                    $items = $changelog->items()
                        ->noChangelogTag()
                        ->get();
                @endphp

                <div>
                    <ul class="list-disc ml-5">
                        @foreach($items as $item)
                            <li>
                                <div>
                                    <a
                                            href="{{ route('items.show', $item) }}"
                                            class="cursor-pointer hover:underline"
                                    >
                                        {{ $item->title }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @foreach($tags as $tag)
                    <div>
                        <div class="border-b pb-1 mb-1">
                            <h3 class="uppercase font-bold">{{ $tag->name }}</h3>
                        </div>

                        <ul class="list-disc ml-5">
                            @foreach($tag->items as $item)
                                <li>
                                    <div>
                                        <a
                                                href="{{ route('items.show', $item) }}"
                                                class="cursor-pointer hover:underline"
                                        >
                                            {{ $item->title }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
