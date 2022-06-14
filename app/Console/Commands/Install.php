<?php

namespace App\Console\Commands;

use App\Models\User;
use Filament\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class Install extends Command
{
    use CanValidateInput;

    protected $signature = 'roadmap:install';

    protected $description = 'Install Roadmap software.';

    public function handle()
    {
        $user = $this->createUser();
        $user->admin = true;
        $user->save();

        $this->info('User created!');

        $this->runNpm();

        $this->info('All done! You can now login at ' . route('filament.auth.login'));
    }

    protected function createUser()
    {
        return User::create($this->getUserData());
    }

    protected function runNpm()
    {

    }

    protected function getUserData(): array
    {
        return [
            'name' => $this->validateInput(fn() => $this->ask('Name'), 'name', ['required']),
            'email' => $this->validateInput(fn() => $this->ask('Email address'), 'email', ['required', 'email', 'unique:' . User::class]),
            'password' => Hash::make($this->validateInput(fn() => $this->secret('Password'), 'password', ['required', 'min:8'])),
        ];
    }
}
