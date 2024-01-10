<?php

namespace App\Exceptions;

use Throwable;
use Sentry\Laravel\Integration;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });

        $this->renderable(function (InvalidSignatureException $e) {
            return response()->view('errors.link-expired', [], 403);
        });
    }
}
