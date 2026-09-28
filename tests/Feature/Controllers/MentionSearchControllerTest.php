<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Comment;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

test('with no query', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('mention-search'))->assertExactJson([]);
});

test('with query', function () {
    $user = User::factory()->create();

    $testUser = User::factory()->create();

    actingAs($user)
        ->get(route('mention-search', ['query' => $testUser->name]))
        ->assertExactJson([
            ['key' => $testUser->name, 'value' => $testUser->username, 'avatar' => $testUser->getGravatar()]
        ]);
});

test('with query matching a username', function () {
    $user = User::factory()->create();

    $testUser = User::factory()->create(['name' => 'Kenneth Wolfs']);

    actingAs($user)
        ->get(route('mention-search', ['query' => 'kenneth-wo']))
        ->assertJsonPath('0.value', $testUser->username);
});

test('with no query and an item suggests its author and commenters except yourself', function () {
    $user = User::factory()->create();
    $author = User::factory()->create(['name' => 'Kenneth Wolfs']);
    $commenter = User::factory()->create(['name' => 'Dennis Smink']);
    User::factory()->create(['name' => 'Not Involved']);

    $item = Item::factory()->for($author)->create();
    Comment::factory()->for($item)->for($commenter)->create();
    Comment::factory()->for($item)->for($user)->create();

    actingAs($user)
        ->get(route('mention-search', ['item' => $item->id]))
        ->assertExactJson([
            ['key' => $commenter->name, 'value' => $commenter->username, 'avatar' => $commenter->getGravatar()],
            ['key' => $author->name, 'value' => $author->username, 'avatar' => $author->getGravatar()],
        ]);
});

test('with a query the participants of the item are listed first', function () {
    $user = User::factory()->create();
    $author = User::factory()->create(['name' => 'Kenneth Wolfs']);
    User::factory()->create(['name' => 'Alex Kenneth']);

    $item = Item::factory()->for($author)->create();

    actingAs($user)
        ->get(route('mention-search', ['query' => 'kenneth', 'item' => $item->id]))
        ->assertJsonPath('0.key', 'Kenneth Wolfs')
        ->assertJsonPath('1.key', 'Alex Kenneth');
});

test('authors of private comments are not suggested to non admins', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $item = Item::factory()->for($user)->create();
    Comment::factory()->for($item)->for($admin)->create(['private' => true]);

    actingAs($user)
        ->get(route('mention-search', ['item' => $item->id]))
        ->assertExactJson([]);
});

test('participants of an item you cannot see are not suggested', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();

    $item = Item::factory()->private()->for($author)->create();

    actingAs($user)
        ->get(route('mention-search', ['item' => $item->id]))
        ->assertExactJson([]);
});

test('user must be logged in', function () {
    get(route('mention-search'))->assertRedirect('login');
});
