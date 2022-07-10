<div>
    <h3 class="mb-3">{{ trans('items.edit') }}</h3>

    <form wire:submit.prevent="submit" class="space-y-5">
        {{ $this->form }}

        <x-filament::button wire:click="submit">
            {{ trans('items.edit') }}
        </x-filament::button>
    </form>
</div>
