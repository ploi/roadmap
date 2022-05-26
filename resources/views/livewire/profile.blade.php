<div class="space-y-6">
    <form class="space-y-4" wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button wire:click="submit">
            Save
        </x-filament::button>

        <x-filament::button type="button" color="secondary" wire:click="logout">
            Logout
        </x-filament::button>
    </form>

    @if($hasSsoLoginAvailable)
        <div>
            <h2 class="text-lg tracking-tight font-bold">Social login</h2>
            <p class="text-gray-500 text-sm">Here you'll find the social login's you've used to log in with your account.</p>
        </div>

        {{ $this->table }}
    @endif
</div>
