<?php

it('can generate an username upon user creation', function () {
    $user = createUser();

    expect($user->fresh()->username)
        ->toBeString()
        ->toBe(Str::slug($user->name));
});

it('can create an admin user', function () {
    $user = createUser(['admin' => true]);

    expect($user->fresh()->admin)->toBeTruthy();
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
