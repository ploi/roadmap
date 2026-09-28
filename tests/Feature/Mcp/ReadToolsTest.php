<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Project;
use App\Mcp\Tools\GetItemTool;
use App\Mcp\Tools\ListItemsTool;
use App\Mcp\Tools\GetProjectTool;
use App\Mcp\Servers\RoadmapServer;
use App\Mcp\Tools\ListProjectsTool;

it('lists the projects and visible boards the user can see', function () {
    $project = Project::factory()->create(['title' => 'Public project']);
    Board::factory()->for($project)->create(['title' => 'Planned board']);
    Board::factory()->for($project)->create(['title' => 'Hidden board', 'visible' => false]);
    Project::factory()->private()->create(['title' => 'Secret project']);

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(ListProjectsTool::class)
        ->assertOk()
        ->assertSee(['Public project', 'Planned board'])
        ->assertDontSee(['Secret project', 'Hidden board']);
});

it('lists private projects the user is a member of', function () {
    $user = User::factory()->create();
    $project = Project::factory()->private()->create(['title' => 'Members only']);
    $project->members()->attach($user);

    RoadmapServer::actingAs($user)
        ->tool(ListProjectsTool::class)
        ->assertOk()
        ->assertSee('Members only');
});

it('shows hidden boards and private projects to admins', function () {
    $project = Project::factory()->private()->create(['title' => 'Secret project']);
    Board::factory()->for($project)->create(['title' => 'Hidden board', 'visible' => false]);

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(ListProjectsTool::class)
        ->assertOk()
        ->assertSee(['Secret project', 'Hidden board']);
});

it('gets a project by slug with item counts per board', function () {
    $project = Project::factory()->create(['title' => 'Roadmap']);
    $board = Board::factory()->for($project)->create(['title' => 'Planned']);
    Item::factory()->count(3)->for($project)->for($board)->create();
    Item::factory()->private()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(GetProjectTool::class, ['project' => $project->slug])
        ->assertOk()
        ->assertSee(['Roadmap', 'Planned', '"items_count":3']);
});

it('does not get a private project the user is not a member of', function () {
    $project = Project::factory()->private()->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(GetProjectTool::class, ['project' => $project->id])
        ->assertHasErrors(['Project not found.']);
});

it('does not let a member of one private project reach another private project', function () {
    $user = User::factory()->create();
    Project::factory()->private()->create()->members()->attach($user);
    $otherProject = Project::factory()->private()->create();

    RoadmapServer::actingAs($user)
        ->tool(GetProjectTool::class, ['project' => $otherProject->slug])
        ->assertHasErrors(['Project not found.']);
});

it('lists items filtered on project, board and search', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();
    $otherBoard = Board::factory()->for($project)->create();
    Item::factory()->for($project)->for($board)->create(['title' => 'Dark mode support']);
    Item::factory()->for($project)->for($board)->create(['title' => 'Export to CSV']);
    Item::factory()->for($project)->for($otherBoard)->create(['title' => 'Dark theme for emails']);
    Item::factory()->private()->for($project)->for($board)->create(['title' => 'Dark secret']);

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(ListItemsTool::class, ['project' => $project->slug, 'board' => $board->id, 'search' => 'Dark'])
        ->assertOk()
        ->assertSee(['Dark mode support', '"total":1'])
        ->assertDontSee(['Export to CSV', 'Dark theme for emails', 'Dark secret']);
});

it('sorts items by votes', function () {
    Item::factory()->create(['title' => 'Least wanted', 'total_votes' => 1]);
    Item::factory()->create(['title' => 'Most wanted', 'total_votes' => 50]);

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(ListItemsTool::class, ['sort' => 'popular', 'per_page' => 1])
        ->assertOk()
        ->assertSee(['Most wanted', '"has_more_pages":true'])
        ->assertDontSee('Least wanted');
});

it('requires a project when filtering on a board', function () {
    RoadmapServer::actingAs(User::factory()->create())
        ->tool(ListItemsTool::class, ['board' => 1])
        ->assertHasErrors(['A board can only be used together with a project.']);
});

it('gets an item with its public comments', function () {
    $item = Item::factory()->create(['title' => 'Dark mode', 'content' => 'Please add a dark mode.']);
    Comment::factory()->for($item)->for(User::factory())->create(['content' => 'Public comment']);
    Comment::factory()->for($item)->for(User::factory())->create(['content' => 'Private note', 'private' => true]);

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(GetItemTool::class, ['item' => $item->slug])
        ->assertOk()
        ->assertSee(['Dark mode', 'Please add a dark mode.', 'Public comment'])
        ->assertDontSee('Private note');
});

it('shows private notes on an item to employees', function () {
    $item = Item::factory()->create();
    Comment::factory()->for($item)->for(User::factory())->create(['content' => 'Private note', 'private' => true]);

    RoadmapServer::actingAs(User::factory()->create(['role' => UserRole::Employee]))
        ->tool(GetItemTool::class, ['item' => $item->id])
        ->assertOk()
        ->assertSee('Private note');
});

it('does not get a private item for regular users', function () {
    $item = Item::factory()->private()->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(GetItemTool::class, ['item' => $item->id])
        ->assertHasErrors(['Item not found.']);
});
