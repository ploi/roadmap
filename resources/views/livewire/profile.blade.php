<div>
    <form class="space-y-4" wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button wire:click="submit">
            Save
        </x-filament::button>

        <x-filament::button type="button" color="secondary" wire:click="logout">
            Logout
        </x-filament::button>
    </form>
</div>
