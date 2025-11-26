<?php

use App\Models\User;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Mail;

it('can create a UserCreated mailable with correct properties', function () {
    $user = createUser(['name' => 'Test User', 'email' => 'test@example.com']);
    $password = 'TestPassword123!';
    $loginUrl = route('login');

    $mailable = new UserCreated($user, $password, $loginUrl);

    expect($mailable->user)->toBe($user)
        ->and($mailable->password)->toBe($password)
        ->and($mailable->loginUrl)->toBe($loginUrl);
});

it('has the correct subject with app name', function () {
    $user = createUser(['name' => 'Test User']);
    $password = 'TestPassword123!';
    $loginUrl = route('login');

    $mailable = new UserCreated($user, $password, $loginUrl);
    $envelope = $mailable->envelope();

    expect($envelope->subject)->toBe(trans('mail.user-created.subject', ['app_name' => config('app.name')]));
});

it('uses the correct markdown view', function () {
    $user = createUser(['name' => 'Test User']);
    $password = 'TestPassword123!';
    $loginUrl = route('login');

    $mailable = new UserCreated($user, $password, $loginUrl);
    $content = $mailable->content();

    expect($content->markdown)->toBe('emails.user-created');
});

it('should be queued', function () {
    $user = createUser(['name' => 'Test User']);
    $password = 'TestPassword123!';
    $loginUrl = route('login');

    $mailable = new UserCreated($user, $password, $loginUrl);

    expect($mailable)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('renders the email with correct content', function () {
    $user = createUser(['name' => 'Test User', 'email' => 'test@example.com']);
    $password = 'TestPassword123!';
    $loginUrl = route('login');

    $mailable = new UserCreated($user, $password, $loginUrl);
    $rendered = $mailable->render();

    expect($rendered)
        ->toContain($user->name)
        ->toContain($user->email)
        ->toContain($password)
        ->toContain($loginUrl);
});
