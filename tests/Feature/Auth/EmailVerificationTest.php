<?php

namespace App\Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

beforeEach(function () {
    Notification::fake();
});

test('an unverified user can request a new verification email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->from(route('verification.notice'))
        ->post(route('verification.resend'))
        ->assertRedirect(route('verification.notice'))
        ->assertSessionHas('resent', true);

    Notification::assertSentToTimes($user, VerifyEmail::class, 1);
});

test('verification emails are limited to 5 per 5 minutes', function () {
    $user = User::factory()->unverified()->create();

    foreach (range(1, 5) as $attempt) {
        $this->actingAs($user)
            ->post(route('verification.resend'))
            ->assertRedirect();
    }

    $this->actingAs($user)
        ->post(route('verification.resend'))
        ->assertTooManyRequests();

    Notification::assertSentToTimes($user, VerifyEmail::class, 5);

    $this->travel(5)->minutes();

    $this->actingAs($user)
        ->post(route('verification.resend'))
        ->assertRedirect();

    Notification::assertSentToTimes($user, VerifyEmail::class, 6);
});

test('the verification email limit is applied per user', function () {
    $user = User::factory()->unverified()->create();
    $otherUser = User::factory()->unverified()->create();

    foreach (range(1, 5) as $attempt) {
        $this->actingAs($user)->post(route('verification.resend'));
    }

    $this->actingAs($user)
        ->post(route('verification.resend'))
        ->assertTooManyRequests();

    $this->actingAs($otherUser)
        ->post(route('verification.resend'))
        ->assertRedirect();

    Notification::assertSentToTimes($otherUser, VerifyEmail::class, 1);
});

test('guests cannot request a verification email', function () {
    $this->post(route('verification.resend'))
        ->assertRedirect(route('login'));

    Notification::assertNothingSent();
});
