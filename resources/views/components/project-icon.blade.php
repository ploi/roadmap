@props(['project'])

@if ($iconImageUrl = $project->iconImageUrl())
    <img src="{{ $iconImageUrl }}" alt="" {{ $attributes->class('object-contain scheme-light dark:scheme-dark') }} />
@else
    <x-dynamic-component :component="$project->icon ?? 'heroicon-o-hashtag'" {{ $attributes }} />
@endif
