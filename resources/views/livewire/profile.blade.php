<div>
    {{-- Rendered before the form so action modals don't end up inside the social login table, which sits in a hidden tab. --}}
    <x-filament-actions::modals />

    <div class="space-y-6">
        <div class="flex justify-end gap-2">
            {{ $this->viewProfileAction }}
            {{ $this->logoutAction }}
        </div>

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    </div>
</div>
