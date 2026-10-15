<div
    class="space-y-4 border-t border-gray-200 pt-6 dark:border-white/10"
    x-data="{ supported: typeof window.PublicKeyCredential === 'function', busy: false, error: null }"
    data-messages="{{ json_encode(trans('auth.passkeys.errors')) }}"
    x-on:passkey-registration-started.window="
        busy = true;
        error = null;
        window.Passkeys.create($event.detail.options, { messages: JSON.parse($root.dataset.messages) })
            .then((credential) => $wire.finishPasskeyRegistration(credential))
            .catch((exception) => { error = exception.message })
            .finally(() => { busy = false })
    "
>
    <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.passkeys.heading') }}</h2>

    <p class="text-gray-500 text-sm max-w-2xl">{{ trans('profile.passkeys.description') }}</p>

    @if($passkeys->isEmpty())
        <p class="text-sm text-gray-500">{{ trans('profile.passkeys.no_passkeys') }}</p>
    @else
        <ul class="divide-y divide-gray-200 dark:divide-white/10 max-w-2xl">
            @foreach($passkeys as $passkey)
                <li class="flex items-center justify-between gap-4 py-2" wire:key="passkey-{{ $passkey->id }}">
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ $passkey->name }}</p>
                        <p class="text-xs text-gray-500">
                            @if($passkey->authenticator)
                                {{ $passkey->authenticator }} &middot;
                            @endif
                            {{ trans('profile.passkeys.added', ['date' => $passkey->created_at->toFormattedDateString()]) }}
                            &middot;
                            {{ $passkey->last_used_at ? trans('profile.passkeys.last_used', ['date' => $passkey->last_used_at->diffForHumans()]) : trans('profile.passkeys.never_used') }}
                        </p>
                    </div>

                    {{ ($this->deletePasskeyAction)(['passkey' => $passkey->id]) }}
                </li>
            @endforeach
        </ul>
    @endif

    <div x-show="supported" x-cloak class="space-y-2">
        <div class="flex items-center gap-3">
            {{ $this->addPasskeyAction }}

            <span x-show="busy" x-cloak class="text-sm text-gray-500">{{ trans('auth.passkeys.waiting') }}</span>
        </div>

        <p x-show="error" x-text="error" x-cloak class="text-sm text-red-600"></p>
    </div>

    <p x-show="! supported" x-cloak class="text-sm text-gray-500">{{ trans('profile.passkeys.unsupported') }}</p>
</div>
