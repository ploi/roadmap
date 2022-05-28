<x-modal>
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <div>
                Create an item
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

    @auth
        <x-slot name="content">
            {{ $this->form }}
        </x-slot>
    @endauth
    @guest
        <x-slot name="content">
            <p>Login to be able to submit an item.</p>
        </x-slot>
    @endguest

    <x-slot name="buttons">
        @auth
            <x-filament::button wire:click="submit">
                Create
            </x-filament::button>
        @endauth

        <x-filament::button color="secondary" wire:click="$emit('closeModal')">
            Close
        </x-filament::button>
    </x-slot>
</x-modal>
