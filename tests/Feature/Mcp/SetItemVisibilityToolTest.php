<?php

use App\Models\Item;
use App\Models\User;
use App\Enums\UserRole;
use App\Mcp\Servers\RoadmapServer;
use App\Mcp\Tools\SetItemVisibilityTool;
use function Pest\Laravel\assertDatabaseHas;

it('makes an item private', function (UserRole $role) {
    $item = Item::factory()->create(['title' => 'Dark mode']);

    RoadmapServer::actingAs(User::factory()->create(['role' => $role]))
        ->tool(SetItemVisibilityTool::class, ['item' => $item->slug, 'private' => true])
        ->assertOk()
        ->assertSee('Made \"Dark mode\" private.');

    assertDatabaseHas(Item::class, ['id' => $item->id, 'private' => true]);
})->with([UserRole::Admin, UserRole::Employee]);

it('makes a private item public', function () {
    $item = Item::factory()->private()->create();

    RoadmapServer::actingAs(User::factory()->admin()->create())
        ->tool(SetItemVisibilityTool::class, ['item' => $item->id, 'private' => false])
        ->assertOk();

    assertDatabaseHas(Item::class, ['id' => $item->id, 'private' => false]);
});

it('does not let regular users change the visibility of items', function () {
    $item = Item::factory()->create();

    RoadmapServer::actingAs(User::factory()->create(['role' => UserRole::User]))
        ->tool(SetItemVisibilityTool::class, ['item' => $item->id, 'private' => true])
        ->assertHasErrors();

    assertDatabaseHas(Item::class, ['id' => $item->id, 'private' => false]);
});
