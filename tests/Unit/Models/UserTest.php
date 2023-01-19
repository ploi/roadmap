<?php

use App\Models\Item;
use App\Models\Vote;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;

it('can generate an username upon user creation', function () {
    $user = createUser();

    expect($user->fresh()->username)
        ->toBeString()
        ->toBe(Str::slug($user->name));
});

it('can create an admin user', function () {
    $user = createUser(['role' => UserRole::Admin]);

    expect($user->fresh()->hasRole(UserRole::Admin))->toBeTruthy();
});

it('can check if a user wants a specific notification', function () {
    $user = createUser();

    expect($user->fresh()->wantsNotification('receive_mention_notifications'))->toBeTruthy();
});

it('can check if a user does not want a notification', function () {
    $user = createUser();
    $user->notification_settings = [];
    $user->save();

    expect($user->fresh()->wantsNotification('receive_mention_notifications'))->toBeFalsy();
});

it('can check if a user needs to verify their email', function ($mustVerifyEmail, $verifiedAt, $needsVerification) {
    $user = createAndLoginUser(['email_verified_at' => $verifiedAt]);
    app(GeneralSettings::class)->users_must_verify_email = $mustVerifyEmail;

    expect($user->fresh()->needsToVerifyEmail())->toBe($needsVerification);
})->with([
    '! email needs verification && email verified' => [$mustVerifyEmail = false, $verifiedAt = now(), $needsVerification = false],
    '! email needs verification && ! email verified' => [$mustVerifyEmail = false, $verifiedAt = null, $needsVerification = false],
    'email needs verification && email verified' => [$mustVerifyEmail = true, $verifiedAt = now(), $needsVerification = false],
    'email needs verification && ! email verified' => [$mustVerifyEmail = true, $verifiedAt = null, $needsVerification = true],
]);

it('can delete a user', function () {
    $user = createUser();

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can delete a user with items', function () {
    $user = createUser([], ['items' => Item::factory(1)]);

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can delete a user with comments', function () {
    Notification::fake();
    $user = createUser([], ['comments' => Comment::factory(1)->has(Item::factory())]);

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can delete a user with votes', function () {
    $user = createUser();

    $item = Item::factory()->has(Vote::factory())->create();

    $item->user()->associate($user);
    $vote = $item->votes()->first();
    $vote->user()->associate($user);
    $vote->save();
    $item->save();

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can delete a user with items and comments and votes', function () {
    $user = createUser([], [
        'items' => Item::factory(1)->has(Vote::factory()),
        'comments' => Comment::factory(1)->has(Item::factory())
    ]);

    $vote = $user->fresh()->items()->first()->votes()->first();
    $vote->user()->associate($user);
    $vote->save();

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can delete a user with social users', function () {
    $user = createUser([], [
        'userSocials' => \App\Models\UserSocial::factory(),
    ]);

    $user->delete();

    expect($user->fresh())->toBeNull();
});

it('can return true if a user is a subscribed to an item', function () {

    $user = createUser();
    $item = Item::factory()->create();

    DB::table('votes')->insert([
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => true,
    ]);

    $this->assertTrue($user->isSubscribedToItem($item));
});

it('can return false if a user is not subscribed to an item', function () {

    $user = createUser();
    $item = Item::factory()->create();

    DB::table('votes')->insert([
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => false,
    ]);

    $this->assertFalse($user->isSubscribedToItem($item));
});

it('toggles the subscription state of a vote the user belongs to', function () {

    $user = createUser();
    $item = Item::factory()->create();

    DB::table('votes')->insert([
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => false,
    ]);

    $user->toggleVoteSubscription($item->id, Item::class);

    $this->assertTrue($user->isSubscribedToItem($item));
});

it('does not toggle the subscription if the user does not have a vote for that item', function () {

    $user = createUser();
    $item = Item::factory()->create();

    $user->toggleVoteSubscription($item->id, Item::class);

    $this->assertFalse($user->isSubscribedToItem($item));
});
