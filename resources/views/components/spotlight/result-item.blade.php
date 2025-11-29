@props([
    'href' => null,
    'index',
    'icon',
    'iconClass' => '',
    'title',
    'meta' => null,
    'aside' => null,
    'navigate' => true,
    'closeOnClick' => true,
])

@php
    $baseClasses = 'group flex items-start gap-3 rounded-lg px-3 py-2.5 text-left transition-colors hover:bg-gray-100 dark:hover:bg-gray-800';
    $selectedClasses = 'bg-gray-100 dark:bg-gray-800';

    $iconBaseClasses = 'mt-0.5 h-5 w-5 flex-shrink-0';
    $iconDefaultClasses = 'text-gray-400 group-hover:text-blue-500 dark:text-gray-500';
    $iconSelectedClasses = 'text-blue-500 dark:text-blue-400';
@endphp

@if($href)
    <a
        href="{{ $href }}"
        @if($navigate) wire:navigate @endif
        @if($closeOnClick) @click="close()" @endif
        x-bind:data-selected="isItemSelected({{ $index }})"
        class="{{ $baseClasses }} {{ $attributes->get('class') }}"
        x-bind:class="isItemSelected({{ $index }}) ? '{{ $selectedClasses }}' : ''"
    >
        @if($iconClass)
            <span class="mt-0.5 text-xl">{{ $icon }}</span>
        @else
            <x-filament::icon
                :icon="$icon"
                x-bind:class="isItemSelected({{ $index }}) ? '{{ $iconBaseClasses }} {{ $iconSelectedClasses }}' : '{{ $iconBaseClasses }} {{ $iconDefaultClasses }}'"
            />
        @endif
        <div class="min-w-0 flex-1">
            <div class="truncate font-medium text-gray-900 dark:text-white">
                {{ $title }}
            </div>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @elseif($meta)
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ $meta }}
                </div>
            @endif
        </div>
        @if($aside)
            {{ $aside }}
        @endif
    </a>
@else
    <button
        type="button"
        {{ $attributes->except('class') }}
        x-bind:data-selected="isItemSelected({{ $index }})"
        class="{{ $baseClasses }} w-full {{ $attributes->get('class') }}"
        x-bind:class="isItemSelected({{ $index }}) ? '{{ $selectedClasses }}' : ''"
    >
        @if($iconClass)
            <span class="mt-0.5 text-xl">{{ $icon }}</span>
        @else
            <x-filament::icon
                :icon="$icon"
                x-bind:class="isItemSelected({{ $index }}) ? '{{ $iconBaseClasses }} {{ $iconSelectedClasses }}' : '{{ $iconBaseClasses }} {{ $iconDefaultClasses }}'"
            />
        @endif
        <div class="min-w-0 flex-1">
            <div class="truncate font-medium text-gray-900 dark:text-white">
                {{ $title }}
            </div>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @elseif($meta)
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ $meta }}
                </div>
            @endif
        </div>
        @if($aside)
            {{ $aside }}
        @endif
    </button>
@endif