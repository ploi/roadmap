<?php

namespace App\Spotlight;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class Logout extends SpotlightCommand
{
    public function getName(): string
    {
        return trans('spotlight.logout.name');
    }

    public function getDescription(): string
    {
        return trans('spotlight.logout.description');
    }

    public function execute(Spotlight $spotlight, Guard $guard): void
    {
        $guard->logout();
        $spotlight->redirect('/');
    }

    public function shouldBeShown(Request $request): bool
    {
        return (bool)$request->user();
    }
}
