<div class="space-y-6">
    <form class="space-y-4" wire:submit="submit">
        {{ $this->form }}

        <div class="flex justify-between">
            <div>
                <x-filament::button wire:click="submit">
                    {{ trans('profile.save') }}
                </x-filament::button>

                {{ $this->viewProfileAction }}
                {{ $this->logoutAction }}
            </div>

            <div>
                {{ $this->deleteAction }}
            </div>
        </div>
    </form>

    {{-- Two-factor authentication --}}
    <div class="space-y-4 border-t border-gray-200 dark:border-white/10 pt-6">
        <div class="flex items-center gap-3">
            <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.two_factor.heading') }}</h2>

            @if($twoFactorConfirmed)
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">
                    {{ trans('profile.two_factor.status_enabled') }}
                </span>
            @else
                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                    {{ trans('profile.two_factor.status_disabled') }}
                </span>
            @endif
        </div>

        <p class="text-gray-500 text-sm max-w-2xl">{{ trans('profile.two_factor.description') }}</p>

        @if(! $twoFactorEnabled)
            {{-- Disabled: offer to enable --}}
            <div>
                {{ $this->enableTwoFactorAction }}
            </div>
        @elseif(! $twoFactorConfirmed)
            {{-- Enrolling: show QR + setup key, ask for a confirmation code --}}
            <div class="space-y-4">
                <p class="text-sm font-medium">{{ trans('profile.two_factor.setup_instructions') }}</p>

                <div class="inline-block rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                    {!! $twoFactorQrCode !!}
                </div>

                <div class="text-sm text-gray-500">
                    {{ trans('profile.two_factor.setup_key') }}:
                    <code class="select-all font-mono text-gray-700 dark:text-gray-300">{{ $twoFactorSetupKey }}</code>
                </div>

                <div class="flex items-center gap-2">
                    {{ $this->confirmTwoFactorAction }}
                    {{ $this->cancelTwoFactorAction }}
                </div>
            </div>
        @else
            {{-- Enabled + confirmed: show recovery codes and management --}}
            <div class="space-y-4" x-data="{ showCodes: false }">
                <p class="text-sm text-gray-500">{{ trans('profile.two_factor.recovery_codes_description') }}</p>

                <button type="button"
                        class="text-sm font-medium text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                        @click="showCodes = ! showCodes">
                    <span x-show="! showCodes">{{ trans('profile.two_factor.show_recovery_codes') }}</span>
                    <span x-show="showCodes" x-cloak>{{ trans('profile.two_factor.hide_recovery_codes') }}</span>
                </button>

                <div x-show="showCodes" x-cloak
                     class="grid grid-cols-2 gap-2 rounded-lg bg-gray-50 p-4 font-mono text-sm dark:bg-gray-900 max-w-md">
                    @foreach($recoveryCodes as $code)
                        <div class="select-all text-gray-700 dark:text-gray-300">{{ $code }}</div>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    {{ $this->regenerateRecoveryCodesAction }}
                    {{ $this->disableTwoFactorAction }}
                </div>
            </div>
        @endif
    </div>

    @if($hasSsoLoginAvailable)
        <div>
            <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.social-login') }}</h2>
            <p class="text-gray-500 text-sm">{{ trans('profile.social-login-description') }}</p>
        </div>

        {{ $this->table }}
    @endif

    <x-filament-actions::modals />
</div>
