<?php

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
        'project_id' => $project->getAttributeValue('id'),
        'board_id' => $board->getAttributeValue('id'),
        'user_id' => $user,
    ]);
});

test('A user can vote on their own item', function () {

    $user = User::first();
    $item = Item::first();
    $project = Project::first();

    $this->assertEquals(0, $item->votes()->count());

    $this->actingAs($user)
        ->post(route('projects.items.vote', [
            'item' => $item->getAttributeValue('id'),
            'project' => $project->getAttributeValue('id'),
        ]))
        ->assertStatus(302);

    $this->assertAuthenticatedAs($user);
    $this->assertEquals(1, $item->votes()->count());
});

test('Another user can vote on an item', function () {

    $anotherUser = User::find(2);
    $item = Item::first();
    $project = Project::first();

    $this->assertEquals(0, $item->votes()->count());

    $this->actingAs($anotherUser)
        ->post(route('projects.items.vote', [
            'item' => $item->getAttributeValue('id'),
            'project' => $project->getAttributeValue('id'),
        ]))
        ->assertStatus(302);

    $this->assertAuthenticatedAs($anotherUser);
    $this->assertEquals(1, $item->votes()->count());
});

test('A guest cannot vote on an item', function () {

    $item = Item::first();
    $project = Project::first();

    $this->assertEquals(0, $item->votes()->count());

    $this->post(route('projects.items.vote', [
        'item' => $item->getAttributeValue('id'),
        'project' => $project->getAttributeValue('id'),
    ]))->assertStatus(302);

    $this->assertGuest();
    $this->assertEquals(0, $item->votes()->count());
});

test('A user with admin access can vote on an item', function () {

    $user = User::first();
    $user->forceFill(['role' => User::ROLE_ADMIN])->save();

    $item = Item::first();
    $project = Project::first();

    $this->assertEquals(0, $item->votes()->count());

    $this->actingAs($user)
        ->post(route('projects.items.vote', [
            'item' => $item->getAttributeValue('id'),
            'project' => $project->getAttributeValue('id'),
        ]))
        ->assertStatus(302);

    $this->assertAuthenticatedAs($user);
    $this->assertEquals(1, $item->votes()->count());
    $this->assertTrue($user->hasAdminAccess());
});

test('A user cant vote on an item that belongs to a board which disables voting', function () {

    $user = User::first();

    $board = Board::factory()->create(['block_votes' => true]);
    $item = Item::factory()->create([
        'project_id' => $board->getAttributeValue('project_id'),
        'board_id' => $board->getAttributeValue('id'),
        ]);

    $this->assertEquals(0, $item->votes()->count());

    $this->actingAs($user)
        ->post(route('projects.items.vote', [
            'item' => $item->getKey(),
            'project' => $board->getKey(),
        ]));

    $this->assertAuthenticatedAs($user);
    $this->assertEquals(0, $item->votes()->count());
});
