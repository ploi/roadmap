@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ trans('auth.verify-new-success.') }}
                        </div>
                    @endif

                    {{ trans('auth.verify-notice') }}
                    {{ trans('auth.verify-if-not-received') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ trans('auth.verify-request-new') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
