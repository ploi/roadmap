<?php

namespace App\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class ViewProfile extends SpotlightCommand
{
    protected string $name = 'Profile';

    protected string $description = 'View your profile';

    public function execute(Spotlight $spotlight): void
    {
        $spotlight->redirect(route('profile'));
    }

    public function shouldBeShown(Request $request): bool
    {
        return (bool)$request->user();
    }
}
