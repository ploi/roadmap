<?php

use App\Models\Item;
use App\Models\User;

use App\Models\Board;
use App\Models\Project;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use App\Http\Livewire\Item\Comments;
use App\Http\Livewire\Item\VoteButton;

it('renders the items page without a project', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertOk();
});

it('renders the items page with a project', function () {
    $project = Project::factory()->create();

    $board = Board::factory()
        ->for($project)
        ->create();

    $item = Item::factory()
        ->for($project)
        ->for($board)
        ->create();

    get(route('projects.items.show', [$project,$item]))->assertOk();
});

test('view has breadcrumbs without project', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeInOrder(['Dashboard', $item->title]);
});

test('view has breadcrumbs with project', function () {
    $project = Project::factory()->create();

    $board = Board::factory()
        ->for($project)
        ->create();

    $item = Item::factory()
        ->for($project)
        ->for($board)
        ->create();

    get(route('items.show', $item))->assertSeeInOrder([$project->title, $board->title, $item->title]);
});

test('administer link is avaialbe to admins', function () {
    $item = Item::factory()->create();

    $user = User::factory()->admin()->create();


    actingAs($user)->get(route('items.show', $item))->assertSee('Administer item');
});

test('administer link is  not avaialbe to admins', function () {
    $item = Item::factory()->create();

    $user = User::factory()->create();


    actingAs($user)->get(route('items.show', $item))->assertDontSee('Administer item');
});

test('view contains item.comments component', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeLivewire(Comments::class);
});

test('view contains item.vote-button component', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeLivewire(VoteButton::class);
});
