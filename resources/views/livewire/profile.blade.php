<div class="space-y-6">
    <form class="space-y-4" wire:submit="submit">
        {{ $this->form }}

        <div class="flex justify-between">
            <div>
                <x-filament::button wire:click="submit">
                    {{ trans('profile.save') }}
                </x-filament::button>

                {{ $this->logoutAction }}
            </div>

            <div>
                {{ $this->deleteAction }}
            </div>
        </div>
    </form>

    @if($hasSsoLoginAvailable)
        <div>
            <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.social-login') }}</h2>
            <p class="text-gray-500 text-sm">{{ trans('profile.social-login-description') }}</p>
        </div>

        {{ $this->table }}
    @endif

    <x-filament-actions::modals />
</div>
