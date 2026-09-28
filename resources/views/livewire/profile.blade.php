<div class="space-y-6">
    <div class="flex justify-end gap-2">
        {{ $this->viewProfileAction }}
        {{ $this->logoutAction }}
    </div>

    <form wire:submit="submit">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals />
</div>
