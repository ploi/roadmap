<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Project;
use App\Settings\GeneralSettings;
use App\Mcp\Servers\RoadmapServer;
use App\Mcp\Tools\CommentOnItemTool;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

it('comments on an item as the authenticated user', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    RoadmapServer::actingAs($user)
        ->tool(CommentOnItemTool::class, ['item' => $item->slug, 'content' => 'This would be great!'])
        ->assertOk()
        ->assertSee(['Comment added.', 'This would be great!']);

    assertDatabaseHas(Comment::class, [
        'item_id' => $item->id,
        'user_id' => $user->id,
        'content' => 'This would be great!',
        'private' => false,
    ]);
});

it('replies to a comment on the same item', function () {
    $item = Item::factory()->create();
    $parent = Comment::factory()->for($item)->for(User::factory())->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'I agree', 'parent_id' => $parent->id])
        ->assertOk();

    assertDatabaseHas(Comment::class, ['item_id' => $item->id, 'parent_id' => $parent->id, 'content' => 'I agree']);
});

it('does not reply to a comment of another item', function () {
    $item = Item::factory()->create();
    $otherComment = Comment::factory()->for(Item::factory())->for(User::factory())->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'I agree', 'parent_id' => $otherComment->id])
        ->assertHasErrors(['The comment you are replying to does not exist on this item.']);

    assertDatabaseCount(Comment::class, 1);
});

it('does not let regular users reply to private notes', function () {
    $item = Item::factory()->create();
    $privateNote = Comment::factory()->for($item)->for(User::factory())->create(['private' => true]);

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'I agree', 'parent_id' => $privateNote->id])
        ->assertHasErrors(['The comment you are replying to does not exist on this item.']);
});

it('only lets admins and employees post private notes', function (UserRole $role, bool $allowed) {
    $item = Item::factory()->create();

    $response = RoadmapServer::actingAs(User::factory()->create(['role' => $role]))
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'Internal note', 'private' => true]);

    if ($allowed) {
        $response->assertOk()->assertSee('Private note added.');
        assertDatabaseHas(Comment::class, ['item_id' => $item->id, 'content' => 'Internal note', 'private' => true]);
    } else {
        $response->assertHasErrors(['Only admins and employees can post private notes.']);
        assertDatabaseCount(Comment::class, 0);
    }
})->with([
    [UserRole::User, false],
    [UserRole::Employee, true],
    [UserRole::Admin, true],
]);

it('does not comment on items the user cannot see', function () {
    $item = Item::factory()->private()->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'Sneaky comment'])
        ->assertHasErrors(['Item not found.']);

    assertDatabaseCount(Comment::class, 0);
});

it('does not comment when comments are blocked on the board', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->for($project)->create(['block_comments' => true]);
    $item = Item::factory()->for($project)->for($board)->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'Blocked comment'])
        ->assertHasErrors(['Comments are disabled for items on this board.']);

    assertDatabaseCount(Comment::class, 0);
});

it('requires a verified email when the setting is enabled', function () {
    GeneralSettings::fake(['users_must_verify_email' => true]);

    $item = Item::factory()->create();

    RoadmapServer::actingAs(User::factory()->unverified()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'Unverified comment'])
        ->assertHasErrors(['You need to verify your email address before you can comment.']);

    assertDatabaseCount(Comment::class, 0);
});

it('validates the comment content', function () {
    $item = Item::factory()->create();

    RoadmapServer::actingAs(User::factory()->create())
        ->tool(CommentOnItemTool::class, ['item' => $item->id, 'content' => 'Hi'])
        ->assertHasErrors();

    assertDatabaseCount(Comment::class, 0);
});
