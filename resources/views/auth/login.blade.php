<x-app>
    <div class=" relative overflow-hidden flex justify-center">
        <div class="z-10 flex-1 w-full max-w-lg py-8 md:py-16">
            <div class="w-full max-w-md px-4 mx-auto sm:px-6 md:px-8">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                    Sign in
                </h1>

                <p class="mt-1 text-base font-medium text-gray-500">
                    Or
                    <a class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                       href="{{ route('register') }}">register</a>
                    for free.
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
                      action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="email">Email address</label>

                        <input
                            class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            type="email">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-medium text-gray-700"
                               for="password">Password</label>

                        <input
                            class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                            id="password"
                            name="password"
                            type="password">
                    </div>

                    <div class="flex space-x-3">
                        <input
                            class="text-brand-500 transition border border-gray-300 rounded shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500"
                            id="remember"
                            name="remember"
                            type="checkbox">

                        <div class="flex flex-col space-y-1">
                            <label class="inline-block text-sm font-medium leading-4 text-gray-700"
                                   for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button
                        class="flex items-center justify-center w-full h-8 px-3 text-sm font-semibold tracking-tight text-white transition bg-brand-600 rounded-lg shadow hover:bg-brand-500 focus:bg-brand-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-brand-700 focus:ring-2 focus:ring-white focus:ring-inset"
                        type="submit">Log In
                    </button>
                </form>

                <div class="w-4 mx-auto mt-4 border-t border-gray-300"></div>

                <p class="mt-3 text-sm font-medium text-center">
                    <a class="text-brand-600 transition hover:text-brand-500 focus:outline-none focus:underline"
                       href="#">Forgot password?</a>
                </p>
            </div>
        </div>
    </div>
</x-app>
