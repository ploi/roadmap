@section('title', trans('auth.register'))
@section('image', App\Services\OgImageGenerator::make('Register')->withSubject('Roadmap')->withFilename('register.jpg')->generate()->getPublicUrl())

<x-app>
    <div class=" relative overflow-hidden flex justify-center">
        <div class="flex-1 w-full max-w-lg py-8 md:py-16">
            <div class="w-full max-w-md px-4 mx-auto sm:px-6 md:px-8">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                    {{ trans('auth.register') }}
                </h1>

                <p class="mt-1 text-base font-medium text-gray-500">
                    {{ trans('auth.not_new') }}
                    <a class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                       href="{{ route('login') }}">{{ trans('auth.login') }}</a>.
                </p>

                @if ($errors->any())
                    <div class="alert-danger mt-8">
                        @foreach ($errors->all() as $error)
                            <div>{{ ucfirst($error) }}</div>
                        @endforeach
                    </div>
                @endif

                <form class="mt-8 space-y-6 md:mt-12"
                      method="post"
                      action="{{ route('register') }}">
                    @csrf
                    <x-honeypot/>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="name">{{ trans('auth.name') }}</label>

                        <input
                            class="block w-full h-10 p-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-brand-600 focus:ring-1 focus:ring-inset focus:ring-brand-600 dark:bg-gray-900 dark:border-white/10"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            type="text">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="email">{{ trans('auth.email') }}</label>

                        <input
                            class="block w-full h-10 p-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600 dark:bg-gray-900 dark:border-white/10"
                            id="email"
                            placeholder="{{ trans('auth.email_placeholder') }}"
                            name="email"
                            value="{{ old('email') }}"
                            type="email">
                    </div>

                    <div x-data="{ show: false }" class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700" for="password">{{ trans('auth.password') }}</label>

                        <div class="flex items-center w-full h-10 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-brand-600 focus:ring-1 focus:ring-inset focus:ring-brand-600 dark:bg-gray-900 dark:border-white/10">
                            <input
                                :type="show ? 'text' : 'password'"
                                class="flex-1 h-full px-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600 dark:bg-gray-900 dark:border-white/10"
                                id="password"
                                name="password">

                            <button
                                type="button"
                                class="inline-flex items-center justify-center h-full px-3 text-gray-500 transition rounded-r-lg hover:text-gray-700 focus:outline-none dark:hover:text-gray-300"
                                @click="show = !show"
                                :aria-label="show ? 'Hide password' : 'Show password'">
                                <span x-show="!show" x-cloak>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.489 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <span x-show="show" x-cloak>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l11.544 11.544M21 21l-3.146-3.146" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 9.88a3 3 0 004.242 4.24" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: false }" class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700" for="password_confirmation">{{ trans('auth.confirm_password') }}</label>

                        <div class="flex items-center w-full h-10 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-brand-600 focus:ring-1 focus:ring-inset focus:ring-brand-600 dark:bg-gray-900 dark:border-white/10">
                            <input
                                :type="show ? 'text' : 'password'"
                                class="flex-1 h-full px-2.5 bg-white transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600 dark:bg-gray-900 dark:border-white/10"
                                id="password_confirmation"
                                name="password_confirmation">

                            <button
                                type="button"
                                class="inline-flex items-center justify-center h-full px-3 text-gray-500 transition rounded-r-lg hover:text-gray-700 focus:outline-none dark:hover:text-gray-300"
                                @click="show = !show"
                                :aria-label="show ? 'Hide password confirmation' : 'Show password confirmation'">
                                <span x-show="!show" x-cloak>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.489 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <span x-show="show" x-cloak>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l11.544 11.544M21 21l-3.146-3.146" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 9.88a3 3 0 004.242 4.24" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <button
                        class="flex items-center justify-center w-full h-8 px-3 text-sm font-semibold tracking-tight text-white transition bg-brand-600 rounded-lg shadow hover:bg-brand-500 focus:bg-brand-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-brand-700 focus:ring-2 focus:ring-white focus:ring-inset"
                        type="submit">{{ trans('auth.register') }}
                    </button>
                </form>


                <div class="w-4 mx-auto mt-4 border-t border-gray-300"></div>

                <p class="mt-3 text-sm font-medium text-center">
                    <a class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                       href="{{ route('password.request') }}">{{ trans('auth.forgot_password') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-app>
