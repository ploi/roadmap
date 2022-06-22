<x-modal>
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <div>
                {{ trans('comments.activity') }}
            </div>
            <div class="text-medium">
                <button wire:click="$emit('closeModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="overflow-hidden bg-white shadow rounded-xl">
            <table class="w-full text-left divide-y table-auto">
                <thead>
                    <tr class="divide-x bg-gray-50">
                        <th class="w-1/3 px-4 py-2 text-sm font-semibold text-gray-600">
                            {{ trans('table.updated_at') }}
                        </th>
                        <th class="px-4 py-2 text-sm font-semibold text-gray-600">
                            {{ trans('comments.comment') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y whitespace-nowrap">
                    @foreach($comment->activities()->latest()->get() as $activity)
                        <tr class="divide-x">
                            <td class="px-4 py-3">{{ $activity->created_at->isoFormat('L LTS') }}</td>
                            <td class="px-4 py-3">{{ $activity->changes['old']['content'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <x-filament::button color="secondary" wire:click="$emit('closeModal')">
            {{ trans('general.close') }}
        </x-filament::button>
    </x-slot>
</x-modal>
