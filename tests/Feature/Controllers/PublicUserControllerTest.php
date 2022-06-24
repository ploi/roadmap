<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

test('it renders the page', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('public-user', $user->username))->assertOk();
});

test('user must be logged in', function () {
    $user = User::factory()->create();

    get(route('public-user', $user->username))->assertRedirect(route('login'));
});
