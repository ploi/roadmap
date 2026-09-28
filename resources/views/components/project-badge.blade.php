@props(['project', 'href' => null])

<a href="{{ $href ?? route('projects.show', $project) }}"
   {{ $attributes->class('inline-flex items-center gap-1.5 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 transition hover:bg-gray-200 hover:text-gray-900 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10 dark:hover:text-white') }}>
    <x-project-icon :project="$project" class="size-3.5 shrink-0"/>
    <span class="truncate">{{ $project->title }}</span>
</a>
