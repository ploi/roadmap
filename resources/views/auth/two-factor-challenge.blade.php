@section('title', trans('auth.two_factor.title'))

<x-app>
    <div class="relative overflow-hidden flex justify-center">
        <div class="flex-1 w-full max-w-lg py-8 md:py-16">
            <div class="w-full max-w-md px-4 mx-auto sm:px-6 md:px-8"
                 x-data="{ recovery: false }">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                    {{ trans('auth.two_factor.title') }}
                </h1>

                <p class="mt-1 text-base font-medium text-gray-500"
                   x-show="! recovery">
                    {{ trans('auth.two_factor.challenge_help') }}
                </p>
                <p class="mt-1 text-base font-medium text-gray-500"
                   x-show="recovery"
                   x-cloak>
                    {{ trans('auth.two_factor.recovery_help') }}
                </p>

                @if ($errors->any())
                    <div class="alert-danger mt-8 overflow-scroll">
                        @foreach ($errors->all() as $error)
                            <div>{{ ucfirst($error) }}</div>
                        @endforeach
                    </div>
                @endif

                <form class="mt-8 space-y-6 md:mt-12"
                      method="post"
                      action="{{ route('two-factor.login.store') }}">
                    @csrf

                    {{-- Authenticator code --}}
                    <div class="space-y-2" x-show="! recovery">
                        <label class="inline-block text-sm font-medium dark:text-white text-gray-700"
                               for="code">{{ trans('auth.two_factor.code') }}</label>

                        <input
                            class="block w-full h-10 p-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600 dark:bg-gray-900 dark:border-white/10"
                            id="code"
                            name="code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            autofocus
                            x-bind:disabled="recovery">
                    </div>

                    {{-- Recovery code --}}
                    <div class="space-y-2" x-show="recovery" x-cloak>
                        <label class="inline-block text-sm font-medium dark:text-white text-gray-700"
                               for="recovery_code">{{ trans('auth.two_factor.recovery_code') }}</label>

                        <input
                            class="block w-full h-10 p-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600 dark:bg-gray-900 dark:border-white/10"
                            id="recovery_code"
                            name="recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                            x-bind:disabled="! recovery">
                    </div>

                    <button
                        class="flex items-center justify-center w-full h-8 px-3 text-sm font-semibold tracking-tight text-white transition bg-brand-600 rounded-lg shadow hover:bg-brand-500 focus:bg-brand-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-brand-700 focus:ring-2 focus:ring-white focus:ring-inset"
                        type="submit">{{ trans('auth.login') }}
                    </button>
                </form>

                <div class="w-4 mx-auto mt-4 border-t border-gray-300 dark:border-white/10"></div>

                <p class="mt-3 text-sm font-medium text-center">
                    <button type="button"
                            class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                            x-show="! recovery"
                            @click="recovery = true; $nextTick(() => $refs.recoveryInput?.focus())">
                        {{ trans('auth.two_factor.use_recovery') }}
                    </button>
                    <button type="button"
                            class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                            x-show="recovery"
                            x-cloak
                            @click="recovery = false">
                        {{ trans('auth.two_factor.use_code') }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</x-app>
