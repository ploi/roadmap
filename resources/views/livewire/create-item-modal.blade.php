<x-modal>
    <x-slot name="title">
        Create an item
    </x-slot>

    <x-slot name="content">
        {{ $this->form }}
    </x-slot>

    <x-slot name="buttons">
        <x-filament::button wire:click="submit">
            Create
        </x-filament::button>
    </x-slot>
</x-modal>