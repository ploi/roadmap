<?php

use App\Models\Item;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use function Pest\Laravel\get;
use App\Livewire\Item\Comments;
use function Pest\Laravel\post;
use App\Livewire\Item\VoteButton;
use function PHPUnit\Framework\assertEquals;

it('renders the items page without a project', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertOk();
});

it('renders the items page with a project', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $item = Item::factory()->for($project)->for($board)->create();

    get(route('projects.items.show', [$project, $item]))->assertOk();
});

test('view has breadcrumbs without project', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeInOrder(['Dashboard', $item->title]);
});

test('view has breadcrumbs with project', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $item = Item::factory()->for($project)->for($board)->create();

    get(route('items.show', $item))->assertSeeInOrder([$project->title, $board->title, $item->title]);
});

test('administer link is only available to users that can access filament', function (UserRole $userRole, bool $shouldBeVisible) {
    $item = Item::factory()->create();

    createAndLoginUser(['role' => $userRole]);

    get(route('items.show', $item))->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}('Administer item');
})->with([
    [UserRole::User, false],
    [UserRole::Employee, true],
    [UserRole::Admin, true],
]);

test('view contains item.comments component', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeLivewire(Comments::class);
});

test('view contains item.vote-button component', function () {
    $item = Item::factory()->create();

    get(route('items.show', $item))->assertSeeLivewire(VoteButton::class);
});

test('user can not view private item', function (?UserRole $userRole, int $expectedStatusCode) {
    $item = Item::factory()->private()->create();

    if ($userRole !== null) {
        createAndLoginUser(['role' => $userRole]);
    }

    get(route('items.show', $item))->assertStatus($expectedStatusCode);
})->with([
    [null, 404],
    [UserRole::User, 404],
    [UserRole::Employee, 200],
    [UserRole::Admin, 200],
]);

test('user can not see private note field', function (UserRole $userRole, bool $shouldBeVisible) {
    $item = Item::factory()->private()->create();

    createAndLoginUser(['role' => $userRole]);

    get(route('items.show', $item))->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}(trans('comments.private-note'));
})->with([
    [UserRole::User, false],
    [UserRole::Employee, true],
    [UserRole::Admin, true],
]);

test('user cannot change board of an item', function (UserRole $userRole, bool $shouldBeVisible) {
    $project = Project::factory()->create();
    $boards = Board::factory()->for($project)->count(3)->create();
    $item = Item::factory()->for($boards->first())->for($project)->create();

    createAndLoginUser(['role' => $userRole]);

    get(route('projects.items.show', [$item->project, $item]))
        ->{$shouldBeVisible ? 'assertSee' : 'assertDontSee'}('<select name="board_id"', false);

    $newBoard = $boards->skip(1)->first();
    $postRequest = post(route('projects.items.update-board', [$item->project, $item]), [
        'board_id' => $newBoard->id,
    ]);
    if ($shouldBeVisible) {
        $postRequest->assertRedirect();

        $item->refresh();
        assertEquals($newBoard->id, $item->board->id);
    } else {
        $postRequest->assertForbidden();
    }
})->with([
    'User' => [UserRole::User, false],
    'Employee' => [UserRole::Employee, true],
    'Admin' => [UserRole::Admin, true],
]);
