<?php

use App\Models\Item;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use function Pest\Laravel\get;
use App\Http\Livewire\Item\Create;
use App\Http\Livewire\Project\Items;

use Illuminate\Database\Eloquent\Factories\Sequence;

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

test('private items are not visible for users', function (UserRole $userRole, bool $shouldBeVisible) {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)
                  ->has(
                      Item::factory(2)->state(new Sequence(
                          ['title' => 'item 1', 'private' => false],
                          ['title' => 'item 2', 'private' => true]
                      ))
                  )->create();

    createAndLoginUser(['role' => $userRole]);

    get(route('projects.boards.show', [$project, $board]))
        ->assertSeeText('item 1')
        ->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}('item 2');
})->with([
    'User' => [UserRole::User, false],
    'Employee' => [UserRole::Employee, true],
    'Admin' => [UserRole::Admin, true],
]);
