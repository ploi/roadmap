<?php

use App\Http\Livewire\Item\Create;
use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use Livewire\Livewire;

beforeEach(function () {
    // disables Observers/ItemObserver.php
    Item::unsetEventDispatcher();

    $project = Project::factory()->create();
    Board::factory()->create(['project_id' => $project->getAttributeValue('id')]);
});

test('A user can submit a new item to the roadmap', function () {

    $user = createUser();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit');

    $item = $user->items()->first();

    $this->assertEquals('An example title', $item->getAttributeValue('title'));
    $this->assertEquals('The content of an item.', $item->getAttributeValue('content'));
    $this->assertEquals(1, $item->votes()->where('user_id', $user->getAttributeValue('id'))->count());

});

test('validation rules are enforced when creating an item', function () {

    Livewire::test(Create::class)
        ->set('title', '')
        ->set('content', '')
        ->call('submit')
        ->assertHasErrors(['title' => 'required', 'content' => 'required']);
});

test('redirected to /home if item does not belong to a project', function () {

    Livewire::test(Create::class)
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit')
        ->assertRedirect(route('home'));
});

test('redirected to project view if item belongs to a project', function () {

    $project = Project::first();
    $board = Board::first();

    Livewire::test(Create::class, ['project' => $project, 'board' => $board])
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit')
        ->assertRedirect(route('projects.boards.show',
            [$project->getAttributeValue('id'), $board->getAttributeValue('id')]));
});

// Boards can have "can_users_create" set to false which disables the creation of items. Let's check for this!
test('an item isnt created if it belongs to a board which disables item creation', function () {

    $project = Project::first();
    $board = Board::factory()->create(['project_id' => $project->getKey(), 'can_users_create' => false]);

    Livewire::test(Create::class, ['project' => $project, 'board' => $board])
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit')
        ->assertRedirect(route('home'));

    $this->assertFalse($board->canUsersCreateItem());
    $this->assertEquals(0, Item::count());
});
