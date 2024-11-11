<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class SsoException extends Exception
{
    public function render(): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            $this->getMessage()
        ]);
    }
}
