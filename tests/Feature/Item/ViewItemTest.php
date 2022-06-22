<?php

use App\Enums\UserRole;
use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    // disables Observers/ItemObserver.php
    Item::unsetEventDispatcher();

    $user = createUser();

    // second user
    createUser();

    $project = Project::factory()->create();
    $board = Board::factory()->create(['project_id' => $project->getAttributeValue('id')]);
    Item::factory()->create([
        'project_id' => $project->getAttributeValue('id'), 'board_id' => $board->getAttributeValue('id'),
        'user_id' => $user
    ]);
});

test('A user can render their public item page that has no associated project', function () {

    $user = User::first();
    $item = Item::factory()->create(['user_id' => $user]);

    $this->actingAs($user)
        ->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($user);
    $this->assertEquals($user->getAttributeValue('id'), $item->user->getAttributeValue('id'));
});

test('A user can render their own public item page that has an associated project', function () {

    $user = User::first();
    $project = Project::first();
    $item = Item::first();

    $this->actingAs($user)
        ->get(route('projects.items.show', [
            'item' => $item->getAttributeValue('slug'),
            'project' => $project->getAttributeValue('slug'),
        ]))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($user);
    $this->assertEquals($user->getAttributeValue('id'), $item->user->getAttributeValue('id'));
});

test('A guest can render a public item page that has no associated project', function () {

    $item = Item::first();

    $this->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertGuest();
});

test('A guest can render a public item page that has an associated project', function () {

    $project = Project::first();
    $item = Item::first();

    $this->get(route('projects.items.show', [
            'item' => $item->getAttributeValue('slug'),
            'project' => $project->getAttributeValue('slug'),
        ]))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertGuest();
});

test('Another user can render a public item page that has no associated project', function () {

    $userTwo = User::find(2);
    $item = Item::factory()->create();

    $this->actingAs($userTwo)
        ->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($userTwo);
});

test('Another user can render a own public item page that has an associated project', function () {

    $userTwo = User::find(2);
    $project = Project::first();
    $item = Item::first();

    $this->actingAs($userTwo)
        ->get(route('projects.items.show', [
            'item' => $item->getAttributeValue('slug'),
            'project' => $project->getAttributeValue('slug'),
        ]))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($userTwo);
});

test('A user cant render their private item page that has no associated project', function () {

    $user = createUser();

    $item = Item::factory()->create(['user_id' => $user, 'private' => true]);

    $this->actingAs($user)
        ->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(404);

    $this->assertAuthenticatedAs($user);
    $this->assertEquals($user->getAttributeValue('id'), $item->user->getAttributeValue('id'));
    $this->assertTrue($item->isPrivate());
});

test('A user cant render their own private item page that has an associated project', function () {

    $user = createUser();

    $project = Project::factory()->create();
    $board = Board::factory()->create(['project_id' => $project->getAttributeValue('id')]);
    $item = Item::factory()->create([
        'project_id' => $project->getAttributeValue('id'), 'board_id' => $board->getAttributeValue('id'),
        'user_id' => $user,
        'private' => true,
    ]);

    $this->actingAs($user)
        ->get(route('projects.items.show', [
            'item' => $item->getAttributeValue('slug'),
            'project' => $project->getAttributeValue('slug'),
        ]))
        ->assertStatus(404);

    $this->assertAuthenticatedAs($user);
    $this->assertEquals($user->getAttributeValue('id'), $item->user->getAttributeValue('id'));
    $this->assertTrue($item->isPrivate());
});

test('A guest cant render a private item page that has no associated project', function () {

    $user = createUser();

    $item = Item::factory()->create(['user_id' => $user, 'private' => true]);

    $this->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(404);

    $this->assertGuest();
    $this->assertTrue($item->isPrivate());
});

test('A guest cant render a private item page that has an associated project', function () {

    private_item_setup();

    $project = Project::skip(1)->first();
    $item = Item::skip(1)->first();

    $this->get(route('projects.items.show', [
        'item' => $item->getAttributeValue('slug'),
        'project' => $project->getAttributeValue('slug'),
    ]))
        ->assertStatus(404);

    $this->assertGuest();
    $this->assertTrue($item->isPrivate());
});

test('A user with admin access can render a private item page that has no associated project', function () {

    $user = createUser();
    $user->forceFill(['role' => UserRole::Admin]);

    $item = Item::factory()->create(['user_id' => $user, 'private' => true]);

    $this->actingAs($user)
        ->get(route('items.show', $item->getAttributeValue('slug')))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($user);
    $this->assertTrue($user->hasAdminAccess());
});

test('A user with admin access can render their a private item page that has an associated project', function () {

    $user = createUser();
    $user->forceFill(['role' => UserRole::Admin]);

    private_item_setup();

    $project = Project::skip(1)->first();
    $item = Item::skip(1)->first();

    $this->actingAs($user)
        ->get(route('projects.items.show', [
            'item' => $item->getAttributeValue('slug'),
            'project' => $project->getAttributeValue('slug'),
        ]))
        ->assertStatus(200)
        ->assertViewIs('item');

    $this->assertAuthenticatedAs($user);
    $this->assertTrue($user->hasAdminAccess());
});

function private_item_setup(): void
{
    $project = Project::factory()->create();
    $board = Board::factory()->create(['project_id' => $project->getAttributeValue('id')]);
    $item = Item::factory()->create([
        'project_id' => $project->getAttributeValue('id'), 'board_id' => $board->getAttributeValue('id'),
        'user_id' => User::first(),
        'private' => true,
    ]);
}
