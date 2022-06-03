@section('title', 'Reset password')
@section('image', App\Services\OgImageGenerator::make('Reset password')->withSubject('Roadmap')->withFilename('reset-password.jpg')->generate()->getPublicUrl())

<x-app>
    <div class=" relative overflow-hidden flex justify-center">
        <div class="z-10 flex-1 w-full max-w-lg py-8 md:py-16">
            <div class="w-full max-w-md px-4 mx-auto sm:px-6 md:px-8">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                    {{ trans('auth.reset_password') }}
                </h1>

                @if (session('status'))
                    <div class="alert alert-success mt-8" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-danger mt-8 overflow-scroll">
                        @foreach ($errors->all() as $error)
                            <div>{{ ucfirst($error) }}</div>
                        @endforeach
                    </div>
                @endif

                <form class="mt-8 space-y-6 md:mt-12"
                      method="post"
                      action="{{ route('password.update') }}">

                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="email">{{ trans('auth.email') }}</label>

                        <input
                            class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                            id="email"
                            value="{{ $email ?? old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            name="email"
                            type="email">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="password">{{ trans('auth.password') }}</label>

                        <input
                            class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                            id="password"
                            required
                            autofocus
                            name="password"
                            type="password">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="password_confirmation">{{ trans('auth.confirm_password') }}</label>

                        <input
                            class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                            id="password_confirmation"
                            required
                            autofocus
                            name="password_confirmation"
                            type="password">
                    </div>

                    <button
                        class="flex items-center justify-center w-full h-8 px-3 text-sm font-semibold tracking-tight text-white transition bg-brand-600 rounded-lg shadow hover:bg-brand-500 focus:bg-brand-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-brand-700 focus:ring-2 focus:ring-white focus:ring-inset"
                        type="submit">
                        {{ trans('auth.reset_password') }}
                    </button>

                    <div class="w-4 mx-auto mt-4 border-t border-gray-300"></div>

                    <p class="mt-3 text-sm font-medium text-center">
                        <a class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                           href="{{ route('login') }}">{{ trans('auth.back_to_login') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app>
