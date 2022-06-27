@section('title', trans('auth.verify-email'))
@section('image', App\Services\OgImageGenerator::make('Verify email')->withSubject('Roadmap')->withFilename('verify-email.jpg')->generate()->getPublicUrl())

<x-app>
    <div class=" relative overflow-hidden flex justify-center">
        <div class="z-10 flex-1 w-full max-w-lg py-8 md:py-16">
            <div class="w-full max-w-lg px-4 mx-auto sm:px-6 md:px-8 space-y-4">
                <h1 class="text-xl font-semibold tracking-tight md:text-2xl">
                    {{ trans('auth.verify-email') }}
                </h1>

                @if (session('resent'))
                    <div class="alert-success" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                <div class="alert-info">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="border-b border-dotted border-blue-500 font-semibold">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app>
