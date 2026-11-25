<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        // Livewire shows a failed update's response in a modal and boots the page in it. The app's error
        // pages mount the layout's components, which the update endpoint signs without the path of a page,
        // so every update they send would be rejected. The endpoint answers with the status text instead.
        $this->renderable(function (HttpExceptionInterface $exception, Request $request) {
            if (! $request->routeIs('*livewire.update')) {
                return null;
            }

            $statusCode = $exception->getStatusCode();

            return response(Response::$statusTexts[$statusCode] ?? '', $statusCode, $exception->getHeaders());
        });

        $this->renderable(function (InvalidSignatureException $e) {
            return response()->view('errors.link-expired', [], 403);
        });
    }
}
