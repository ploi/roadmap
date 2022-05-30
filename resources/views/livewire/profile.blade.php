<div class="space-y-6">
    <form class="space-y-4" wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="flex justify-between">
            <div>
                <x-filament::button wire:click="submit">
                    Save
                </x-filament::button>

                <x-filament::button type="button" color="secondary" wire:click="logout">
                    Logout
                </x-filament::button>
            </div>

            <div>
                <x-filament::button type="button" color="danger" wire:click="deleteConfirm">
                    Remove account
                </x-filament::button>
            </div>
        </div>
    </form>

    @if($hasSsoLoginAvailable)
        <div>
            <h2 class="text-lg tracking-tight font-bold">Social login</h2>
            <p class="text-gray-500 text-sm">Here you'll find the social login's you've used to log in with your
                account.</p>
        </div>

        {{ $this->table }}
    @endif

    <x-filament::modal id="deleteAccount">
        <x-slot name="trigger">
            <button type="button">Delete profile</button>
        </x-slot>

        <x-slot name="heading">
            Are you sure you'd like to delete?
        </x-slot>

        This cannot be undone.

        <x-slot name="footer">
            <x-filament::modal.actions full-width>
                <x-filament::button wire:click="closeDeleteConfirm" color="secondary">
                    Cancel
                </x-filament::button>

                <x-filament::button wire:click="delete" color="danger">
                    Delete comment
                </x-filament::button>
            </x-filament::modal.actions>
        </x-slot>
    </x-filament::modal>
</div>
