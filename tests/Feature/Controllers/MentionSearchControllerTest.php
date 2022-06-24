<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('with no query', function () {

    $user = User::factory()->create();

    actingAs($user)->get(route('mention-search'))->assertExactJson([]);
});

test('with query', function () {

    $user = User::factory()->create();

    $testUser = User::factory()->create();

    actingAs($user)
        ->get(route('mention-search',['query' => $testUser->name]))
        ->assertExactJson([
            ['key' => $testUser->name, 'value' => $testUser->username, 'avatar' => $testUser->getGravatar()]
        ]);
});

test('user must be logged in', function () {

    get(route('mention-search'))->assertRedirect('login');
});
