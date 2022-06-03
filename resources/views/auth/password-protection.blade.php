<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password protection - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {!! $brandColors !!}
</head>
<body>
<div class="min-h-[720px] flex justify-center">
    <div class="w-full max-w-lg py-8 bg-white md:py-16">
        <div class="w-full max-w-md px-4 mx-auto sm:px-6 md:px-8">
            <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                {{ trans('auth.password_protected') }}
            </h1>

            @if ($errors->any())
                <div class="alert-danger mt-8">
                    @foreach ($errors->all() as $error)
                        <div>{{ ucfirst($error) }}</div>
                    @endforeach
                </div>
            @endif

            <form class="mt-8 space-y-6 md:mt-12"
                  action="{{ route('password.protection.login') }}" method="post">
                @csrf
                <div class="space-y-2">
                    <label class="inline-block text-sm font-medium text-gray-700"
                           for="password">{{ trans('auth.password') }}</label>

                    <input
                        class="block w-full h-10 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 focus:border-brand-600"
                        id="password"
                        name="password"
                        type="password">
                </div>

                <button
                    class="flex items-center justify-center w-full h-8 px-3 text-sm font-semibold tracking-tight text-white transition bg-brand-600 rounded-lg shadow hover:bg-brand-500 focus:bg-brand-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-brand-700 focus:ring-2 focus:ring-white focus:ring-inset"
                    type="submit">{{ trans('auth.continue') }}</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
