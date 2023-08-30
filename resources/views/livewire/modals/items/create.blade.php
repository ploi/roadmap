<x-modal>
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <div>
                {{ trans('items.create') }}
            </div>
            <div class="text-medium">
                <button wire:click="$dispatch('closeModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

    </x-slot>

    @auth
        <x-slot name="content">
            @if(! auth()->user()->needsToVerifyEmail())
                <div @class(['hidden' => !$similarItems->count()])>
                    <h3 class="mb-2">{{ trans('items.similar-results') }}</h3>
                    <ul class="max-h-20 overflow-y-auto list-disc list-inside">
                        @foreach($similarItems as $similarItem)
                            <li>
                                <a href="{{ route('items.show', $similarItem->slug ?? '') }}"
                                class="border-b border-brand-500 border-dotted text-brand-500 hover:text-brand-700">
                                    <span class="truncate"> {{ $similarItem->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{ $this->form }}
            @else
            <div class="alert-info">
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="border-b border-dotted border-blue-500 font-semibold">{{ __('click here to request another') }}</button>.
                </form>
            </div>
            @endif
        </x-slot>
    @endauth
    @guest
        <x-slot name="content">
            <p>{{ trans('items.login_to_submit_item') }}</p>
        </x-slot>
    @endguest

    <x-slot name="buttons">
        @auth
            @if(!auth()->user()->needsToVerifyEmail())
                <x-filament::button wire:click="submit">
                    {{ trans('items.create') }}
                </x-filament::button>
            @endif()
        @endauth

        <x-filament::button color="secondary" wire:click="$dispatch('closeModal')">
            {{ trans('general.close') }}
        </x-filament::button>
    </x-slot>
</x-modal>
