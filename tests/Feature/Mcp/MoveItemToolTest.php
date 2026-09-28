<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use App\Mcp\Tools\MoveItemTool;
use App\Mcp\Servers\RoadmapServer;
use function Pest\Laravel\assertDatabaseHas;

it('moves an item to another board in the same project', function (UserRole $role) {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $newBoard = Board::factory()->for($project)->create(['title' => 'In progress']);
    $item = Item::factory()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->create(['role' => $role]))
        ->tool(MoveItemTool::class, ['item' => $item->id, 'board' => $newBoard->slug])
        ->assertOk()
        ->assertSee('In progress');

    assertDatabaseHas(Item::class, ['id' => $item->id, 'project_id' => $project->id, 'board_id' => $newBoard->id]);
})->with([UserRole::Admin, UserRole::Employee]);

it('moves an item to a board in another project', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $otherProject = Project::factory()->create();
    $otherBoard = Board::factory()->for($otherProject)->create();
    $item = Item::factory()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(MoveItemTool::class, ['item' => $item->id, 'board' => $otherBoard->id, 'project' => $otherProject->slug])
        ->assertOk();

    assertDatabaseHas(Item::class, ['id' => $item->id, 'project_id' => $otherProject->id, 'board_id' => $otherBoard->id]);
});

it('does not move an item to a board of another project', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $otherBoard = Board::factory()->for(Project::factory())->create();
    $item = Item::factory()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(MoveItemTool::class, ['item' => $item->id, 'board' => $otherBoard->id])
        ->assertHasErrors();

    assertDatabaseHas(Item::class, ['id' => $item->id, 'board_id' => $board->id]);
});

it('does not let regular users move items', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $newBoard = Board::factory()->for($project)->create();
    $item = Item::factory()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->create(['role' => UserRole::User]))
        ->tool(MoveItemTool::class, ['item' => $item->id, 'board' => $newBoard->id])
        ->assertHasErrors();

    assertDatabaseHas(Item::class, ['id' => $item->id, 'board_id' => $board->id]);
});
