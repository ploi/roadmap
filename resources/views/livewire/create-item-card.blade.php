<div>
    <h3 class="mb-3">Create an item</h3>

    <form wire:submit.prevent="submit" class="space-y-5">
        {{ $this->form }}

        <x-filament::button>
            Create
        </x-filament::button>
    </form>
</div>