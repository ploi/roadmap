<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Profile;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\URL;
use App\Notifications\VerifyEmailChange;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'original@example.com',
        'email_verified_at' => now(),
    ]);

    actingAs($this->user);
});

test('profile form validates email format', function () {
    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => 'invalid-email',
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit')
        ->assertHasFormErrors(['email'], 'form');
});

test('profile form validates email uniqueness', function () {
    $otherUser = User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => 'taken@example.com',
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit')
        ->assertHasFormErrors(['email'], 'form');
});

test('user can keep their current email without validation error', function () {
    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit')
        ->assertHasNoFormErrors([], 'form');

    expect($this->user->fresh()->email)->toBe('original@example.com');
});

test('changing email stores pending email and sends verification', function () {
    Notification::fake();

    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => 'newemail@example.com',
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit');

    $this->user->refresh();

    expect($this->user->email)->toBe('original@example.com')
        ->and($this->user->pending_email)->toBe('newemail@example.com');

    Notification::assertSentTimes(VerifyEmailChange::class, 1);
});

test('changing email shows warning notification', function () {
    Notification::fake();

    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => 'newemail@example.com',
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit')
        ->assertNotified();
});

test('email is updated after successful verification', function () {
    $this->user->update([
        'pending_email' => 'newemail@example.com',
    ]);

    $url = URL::temporarySignedRoute(
        'profile.verify-email-change',
        now()->addMinutes(60),
        [
            'id' => $this->user->id,
            'email' => 'newemail@example.com',
        ]
    );

    $response = $this->get($url);

    $response->assertRedirect(route('profile'));

    $this->user->refresh();

    expect($this->user->email)->toBe('newemail@example.com')
        ->and($this->user->pending_email)->toBeNull()
        ->and($this->user->pending_email_verified_at)->not->toBeNull();
});

test('verification fails with invalid signature', function () {
    $this->user->update([
        'pending_email' => 'newemail@example.com',
    ]);

    $url = route('profile.verify-email-change', [
        'id' => $this->user->id,
        'email' => 'newemail@example.com',
    ]);

    $response = $this->get($url);

    $response->assertForbidden();
});

test('verification fails for wrong user', function () {
    $otherUser = User::factory()->create([
        'email' => 'other@example.com',
        'pending_email' => 'newother@example.com',
    ]);

    $url = URL::temporarySignedRoute(
        'profile.verify-email-change',
        now()->addMinutes(60),
        [
            'id' => $otherUser->id,
            'email' => 'newother@example.com',
        ]
    );

    $response = $this->get($url);

    $response->assertForbidden();
});

test('verification fails if pending email does not match', function () {
    $this->user->update([
        'pending_email' => 'different@example.com',
    ]);

    $url = URL::temporarySignedRoute(
        'profile.verify-email-change',
        now()->addMinutes(60),
        [
            'id' => $this->user->id,
            'email' => 'notmatching@example.com',
        ]
    );

    $response = $this->get($url);

    $response->assertStatus(400);
});

test('cannot set email to invalid format', function () {
    Livewire::test(Profile::class)
        ->fillForm([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => 'not-an-email',
            'notification_settings' => [],
            'per_page_setting' => [5],
        ], 'form')
        ->call('submit')
        ->assertHasFormErrors(['email'], 'form');

    expect($this->user->fresh()->email)->toBe('original@example.com');
});
