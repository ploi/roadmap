<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Console\Commands\Concerns\CanShowAnIntro;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class Install extends Command
{
    use CanShowAnIntro;

    protected $signature = 'roadmap:install';

    protected $description = 'Install Roadmap software.';

    public function handle(): void
    {
        $this->intro();
        $this->refreshDatabase();
        $this->createUser();
        $this->linkStorage();
        $this->runNpm();
        $this->publishAssets();

        $this->askForStar();

        $this->writeSeparationLine();
        $this->line(' ');

        info('All done! You can now login at ' . url('/admin'));
    }

    protected function refreshDatabase(): void
    {
        if (confirm(
            label: 'Do you want to run the migrations to set up everything fresh?',
            default: false,
            hint: '(php artisan migrate:fresh)'
        )) {
            $this->call('migrate:fresh');
        }
    }

    protected function createUser(): User
    {
        info('Let\'s create a user.');

        $user = User::create($this->getUserData());
        $user->role = UserRole::Admin;
        $user->email_verified_at = now();
        $user->save();

        info('User created!');

        return $user;
    }

    protected function linkStorage(): void
    {
        if (!file_exists(public_path('storage')) && confirm(
                label: 'Your storage does not seem to be linked, do you want me to do this?',
                hint: '(php artisan storage:link)'
            )
        ) {
            $this->call('storage:link');
        }
    }

    protected function runNpm(): void
    {
        if (confirm('Do you want to run npm ci & npm run production to get the assets ready?')) {
            info('Running NPM...');

            shell_exec('npm ci');
            shell_exec('npm run production');

            info('NPM installation & mixing production done!');
        }
    }

    protected function publishAssets(): void
    {
        info('Publishing assets...');

        $this->call('filament:assets');
    }

    protected function askForStar(): void
    {
        if (User::count() === 1 && confirm(
                label: 'Would you like to show some love by starring the repo?',
                default: true
            )
        ) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/ploi/roadmap');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/ploi/roadmap');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/ploi/roadmap');
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getUserData(): array
    {
        return [
            'name'     => text(
                label: 'Name',
                required: true,
            ),
            'email'    => text(
                label: 'Email address',
                required: true,
                validate: [
                    'email' => ['required', 'email', 'unique:' . User::class]
                ]
            ),
            'password' => Hash::make(password(
                label: 'Password',
                required: true,
                validate: [
                    'password' => ['required', 'min:8']
                ]
            ))
        ];
    }
}
