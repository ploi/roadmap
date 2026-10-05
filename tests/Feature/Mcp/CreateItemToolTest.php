<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use App\Mcp\Tools\CreateItemTool;
use App\Settings\GeneralSettings;
use App\Mcp\Servers\RoadmapServer;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

it('creates an item on a board as the authenticated user', function (UserRole $role) {
    $user = User::factory()->create(['role' => $role]);
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create();

    RoadmapServer::actingAs($user)
        ->tool(CreateItemTool::class, [
            'title' => 'Dark mode',
            'content' => 'Please add a dark mode to the dashboard.',
            'project' => $project->slug,
            'board' => $board->slug,
        ])
        ->assertOk()
        ->assertSee('Dark mode');

    $item = Item::firstWhere('title', 'Dark mode');

    expect($item)
        ->user_id->toBe($user->id)
        ->project_id->toBe($project->id)
        ->board_id->toBe($board->id);

    assertDatabaseHas(Vote::class, ['model_id' => $item->id, 'model_type' => Item::class, 'user_id' => $user->id]);
})->with([UserRole::Admin, UserRole::Employee]);

it('creates an item without a project or board', function () {
    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(CreateItemTool::class, ['title' => 'Dark mode', 'content' => 'Please add a dark mode to the dashboard.'])
        ->assertOk();

    assertDatabaseHas(Item::class, ['title' => 'Dark mode', 'project_id' => null, 'board_id' => null]);
});

it('does not let regular users create items by default', function () {
    RoadmapServer::actingAs(User::factory()->create(['role' => UserRole::User]))
        ->tool(CreateItemTool::class, ['title' => 'Dark mode', 'content' => 'Please add a dark mode to the dashboard.'])
        ->assertHasErrors();

    assertDatabaseCount(Item::class, 0);
});

it('lets regular users create items when an admin allows it', function () {
    GeneralSettings::fake(['mcp_users_can_create_items' => true]);

    $user = User::factory()->create(['role' => UserRole::User]);

    RoadmapServer::actingAs($user)
        ->tool(CreateItemTool::class, ['title' => 'Dark mode', 'content' => 'Please add a dark mode to the dashboard.'])
        ->assertOk();

    assertDatabaseHas(Item::class, ['title' => 'Dark mode', 'user_id' => $user->id]);
});

it('does not let regular users create items on boards that block item creation', function () {
    GeneralSettings::fake(['mcp_users_can_create_items' => true]);

    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create(['can_users_create' => false]);

    RoadmapServer::actingAs(User::factory()->create(['role' => UserRole::User]))
        ->tool(CreateItemTool::class, [
            'title' => 'Dark mode',
            'content' => 'Please add a dark mode to the dashboard.',
            'project' => $project->id,
            'board' => $board->id,
        ])
        ->assertHasErrors();

    assertDatabaseCount(Item::class, 0);
});

it('does not create an item on a board of another project', function () {
    $project = Project::factory()->create();
    $otherBoard = Board::factory()->for(Project::factory())->create();

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(CreateItemTool::class, [
            'title' => 'Dark mode',
            'content' => 'Please add a dark mode to the dashboard.',
            'project' => $project->id,
            'board' => $otherBoard->id,
        ])
        ->assertHasErrors();

    assertDatabaseCount(Item::class, 0);
});

it('requires a project when the settings require one', function () {
    GeneralSettings::fake(['select_project_when_creating_item' => true, 'project_required_when_creating_item' => true]);

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(CreateItemTool::class, ['title' => 'Dark mode', 'content' => 'Please add a dark mode to the dashboard.'])
        ->assertHasErrors(['A project is required, pass the project to create the item in.']);

    assertDatabaseCount(Item::class, 0);
});

it('requires a verified email when the setting is enabled', function () {
    GeneralSettings::fake(['users_must_verify_email' => true]);

    RoadmapServer::actingAs(User::factory()->admin()->unverified()->create())
        ->tool(CreateItemTool::class, ['title' => 'Dark mode', 'content' => 'Please add a dark mode to the dashboard.'])
        ->assertHasErrors(['You need to verify your email address before you can create items.']);

    assertDatabaseCount(Item::class, 0);
});
