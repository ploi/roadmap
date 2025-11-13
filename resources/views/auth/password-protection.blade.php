<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>🔐 Password protection - {{ config('app.name') }}</title>

    @vite('resources/css/app.css')

    {!! $brandColors !!}

    <script>
        function updateTheme() {
            const theme = localStorage.getItem('theme') || 'auto';
            let isDark = false;

            if (theme === 'dark') {
                isDark = true;
            } else if (theme === 'light') {
                isDark = false;
            } else {
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        updateTheme();
    </script>
</head>
<body class="antialiased">
    <!-- Background with gradient -->
    <div class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-brand-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
        @if(app(\App\Settings\ColorSettings::class)->darkmode)
            <!-- Theme toggle - top right corner -->
            <div class="absolute top-6 right-6 z-10" x-data="themeToggle">
                <div class="relative" x-data="{ open: false }">
                    <button
                        type="button"
                        @click="open = !open"
                        @click.away="open = false"
                        class="flex items-center justify-center w-10 h-10 transition rounded-lg bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400"
                    >
                        <x-heroicon-o-sun x-show="theme === 'light'" class="w-5 h-5 text-gray-700"/>
                        <x-heroicon-o-moon x-show="theme === 'dark'" class="w-5 h-5 text-gray-300"/>
                        <x-heroicon-o-computer-desktop x-show="theme === 'auto'" class="w-5 h-5 text-gray-700 dark:text-gray-300"/>
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @click="open = false"
                        class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5 dark:ring-white/10 backdrop-blur-sm"
                    >
                        <div class="py-1">
                            <button
                                type="button"
                                @click="setTheme('light')"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <x-heroicon-o-sun class="w-5 h-5"/>
                                <span class="font-medium">Light</span>
                            </button>
                            <button
                                type="button"
                                @click="setTheme('dark')"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <x-heroicon-o-moon class="w-5 h-5"/>
                                <span class="font-medium">Dark</span>
                            </button>
                            <button
                                type="button"
                                @click="setTheme('auto')"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <x-heroicon-o-computer-desktop class="w-5 h-5"/>
                                <span class="font-medium">Auto</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main content -->
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-8 md:p-10 backdrop-blur-sm transform transition hover:scale-[1.01]">
                    <!-- Logo/App name -->
                    <div class="flex justify-center mb-8">
                        @if(!is_null($logo) && file_exists($logoFile = storage_path('app/public/'.$logo)))
                            <img src="{{ asset('storage/'.$logo) }}?v={{ md5_file($logoFile) }}"
                                 alt="{{ config('app.name') }}"
                                 class="h-12 md:h-16 max-w-full object-contain"/>
                        @else
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                                {{ config('app.name') }}
                            </h2>
                        @endif
                    </div>

                    <!-- Lock icon -->
                    <div class="flex justify-center mb-6">
                        <div class="p-4 bg-brand-100 dark:bg-brand-900/30 rounded-full">
                            <svg class="w-8 h-8 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-center text-gray-900 dark:text-white mb-2">
                        {{ trans('auth.password_protected') }}
                    </h1>

                    <p class="text-center text-gray-600 dark:text-gray-400 mb-8">
                        Enter the password to continue
                    </p>

                    <!-- Error messages -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-center gap-2 text-sm text-red-800 dark:text-red-200">
                                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ ucfirst($error) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('password.protection.login') }}" method="post" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                   for="password">
                                {{ trans('auth.password') }}
                            </label>

                            <input
                                class="block w-full px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-white/10 rounded-lg transition duration-150 focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 focus:border-brand-500 dark:focus:border-brand-400 focus:outline-none"
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter password"
                                autofocus
                                required>
                        </div>

                        <button
                            class="w-full py-3 px-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-600 rounded-lg shadow-lg hover:shadow-xl transform transition hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:focus:ring-offset-gray-800 active:scale-[0.98]"
                            type="submit">
                            {{ trans('auth.continue') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
</body>
</html>
