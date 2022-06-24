<?php

use App\Models\Board;
use App\Models\Project;
use function Pest\Laravel\get;
use App\Http\Livewire\Item\Create;

use App\Http\Livewire\Project\Items;

it('renders the view', function () {
    $project = Project::factory()->create();

    $board = Board::factory()->for($project)->create();

    get(route('projects.boards.show', [$project,$board]))->assertOk();
});

test('breadcrumbs', function () {
    $project = Project::factory()->create();

    $board = Board::factory()->for($project)->create();

    get(route('projects.boards.show', [$project,$board]))->assertSeeInOrder([$project->title, $board->title]);
});

test('view contains project.item component', function () {
    $project = Project::factory()->create();

    $board = Board::factory()->for($project)->create();

    get(route('projects.boards.show', [$project,$board]))->assertSeeLivewire(Items::class);
});

test('view contains item.create if users can create Item for board', function () {
    $project = Project::factory()->create();

    $board = Board::factory(['can_users_create' => true])->for($project)->create();

    get(route('projects.boards.show', [$project,$board]))->assertSeeLivewire(Create::class);
});

test('view does not contain item.create if users cannot create Item for board', function () {
    $project = Project::factory()->create();

    $board = Board::factory(['can_users_create' => false])->for($project)->create();

    get(route('projects.boards.show', [$project,$board]))->assertDontSeeLivewire(Create::class);
});
