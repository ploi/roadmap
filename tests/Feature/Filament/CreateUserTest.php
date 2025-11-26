<?php

use App\Enums\UserRole;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use App\Filament\Resources\Users\Pages\CreateUser as CreateUserPage;

test('admin can access create user page', function () {
    $admin = createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateUserPage::class)
        ->assertSuccessful();
});

test('regular user cannot access create user page', function () {
    $user = createAndLoginUser(['role' => UserRole::User]);

    Livewire::test(CreateUserPage::class)
        ->assertForbidden();
});

test('creating a user generates a password and sends email', function () {
    Mail::fake();
    $admin = createAndLoginUser(['role' => UserRole::Admin]);

    $userData = [
        'name' => 'New Test User',
        'email' => 'newuser@example.com',
        'role' => UserRole::User->value,
    ];

    Livewire::test(CreateUserPage::class)
        ->fillForm($userData)
        ->call('create')
        ->assertHasNoFormErrors();

    $user = \App\Models\User::where('email', 'newuser@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New Test User')
        ->and($user->email)->toBe('newuser@example.com')
        ->and($user->role)->toBe(UserRole::User)
        ->and($user->password)->not->toBeNull();

    Mail::assertQueued(UserCreated::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->user->id === $user->id
            && !empty($mail->password)
            && $mail->loginUrl === route('login');
    });
});

test('created user password is properly hashed', function () {
    Mail::fake();
    $admin = createAndLoginUser(['role' => UserRole::Admin]);

    $userData = [
        'name' => 'Test User With Password',
        'email' => 'passwordtest@example.com',
        'role' => UserRole::Employee->value,
    ];

    Livewire::test(CreateUserPage::class)
        ->fillForm($userData)
        ->call('create')
        ->assertHasNoFormErrors();

    $user = \App\Models\User::where('email', 'passwordtest@example.com')->first();

    expect($user->password)->not->toBeNull()
        ->and(Hash::needsRehash($user->password))->toBeFalse();
});

test('email contains the plain password not the hashed one', function () {
    Mail::fake();
    $admin = createAndLoginUser(['role' => UserRole::Admin]);

    $userData = [
        'name' => 'Another User',
        'email' => 'another@example.com',
        'role' => UserRole::Admin->value,
    ];

    Livewire::test(CreateUserPage::class)
        ->fillForm($userData)
        ->call('create')
        ->assertHasNoFormErrors();

    $user = \App\Models\User::where('email', 'another@example.com')->first();

    Mail::assertQueued(UserCreated::class, function ($mail) use ($user) {
        return $mail->user->id === $user->id
            && $mail->password !== $user->password
            && strlen($mail->password) === 16;
    });
});

test('employee cannot access create user page', function () {
    $employee = createAndLoginUser(['role' => UserRole::Employee]);

    Livewire::test(CreateUserPage::class)
        ->assertForbidden();
});

test('created user receives email with correct translations', function () {
    Mail::fake();
    $admin = createAndLoginUser(['role' => UserRole::Admin]);

    $userData = [
        'name' => 'Translation Test User',
        'email' => 'translation@example.com',
        'role' => UserRole::User->value,
    ];

    Livewire::test(CreateUserPage::class)
        ->fillForm($userData)
        ->call('create')
        ->assertHasNoFormErrors();

    $user = \App\Models\User::where('email', 'translation@example.com')->first();

    Mail::assertQueued(UserCreated::class, function ($mail) use ($user) {
        $mailable = new UserCreated($mail->user, $mail->password, $mail->loginUrl);
        $rendered = $mailable->render();

        return str_contains($rendered, trans('mail.user-created.greeting', ['name' => $user->name]))
            && str_contains($rendered, trans('mail.user-created.login-button'));
    });
});
