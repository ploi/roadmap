<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use App\Models\Comment;
use App\Models\Project;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

test('user must be logged in', function () {
    $user = User::factory()->create();
    get(route('public-user', $user->username))->assertRedirect(route('login'));
});

test('returns 404 for non-existent user', function () {
    actingAs(User::factory()->create())
        ->get(route('public-user', 'nonexistent'))
        ->assertNotFound();
});

test('hides private items from non-admin', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();

    Item::factory()->create(['user_id' => $author->id, 'title' => 'Private', 'private' => true]);

    actingAs($viewer)
        ->get(route('public-user', $author->username))
        ->assertDontSee('Private');
});

test('hides items in private projects from non-members', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();
    $project = Project::factory()->private()->create();

    Item::factory()->create([
        'user_id' => $author->id,
        'title' => 'Private Project Item',
        'project_id' => $project->id,
    ]);

    actingAs($viewer)
        ->get(route('public-user', $author->username))
        ->assertDontSee('Private Project Item');
});

test('shows all items to admin', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->create();

    Item::factory()->create(['user_id' => $author->id, 'title' => 'Private', 'private' => true]);

    actingAs($admin)
        ->get(route('public-user', $author->username))
        ->assertSee('Private');
});

test('shows comments on public items only', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();

    $publicItem = Item::factory()->create(['private' => false]);
    $privateItem = Item::factory()->create(['private' => true]);

    Comment::factory()->create(['user_id' => $author->id, 'item_id' => $publicItem->id, 'content' => 'Public']);
    Comment::factory()->create(['user_id' => $author->id, 'item_id' => $privateItem->id, 'content' => 'Private']);

    actingAs($viewer)
        ->get(route('public-user', $author->username))
        ->assertSee('Public')
        ->assertDontSee('Private');
});

test('hides comments on items in private projects', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();
    $project = Project::factory()->private()->create();

    $item = Item::factory()->create(['project_id' => $project->id]);
    Comment::factory()->create(['user_id' => $author->id, 'item_id' => $item->id, 'content' => 'Hidden']);

    actingAs($viewer)
        ->get(route('public-user', $author->username))
        ->assertDontSee('Hidden');
});

test('shows votes on public items only', function () {
    $viewer = User::factory()->create();
    $voter = User::factory()->create();

    $publicItem = Item::factory()->create(['title' => 'Public', 'private' => false]);
    $privateItem = Item::factory()->create(['title' => 'Private', 'private' => true]);

    Vote::factory()->create(['user_id' => $voter->id, 'model_type' => Item::class, 'model_id' => $publicItem->id]);
    Vote::factory()->create(['user_id' => $voter->id, 'model_type' => Item::class, 'model_id' => $privateItem->id]);

    actingAs($viewer)
        ->get(route('public-user', $voter->username))
        ->assertSee('Public')
        ->assertDontSee('Private');
});

test('hides votes on items in private projects', function () {
    $viewer = User::factory()->create();
    $voter = User::factory()->create();
    $project = Project::factory()->private()->create();

    $item = Item::factory()->create(['title' => 'Hidden', 'project_id' => $project->id]);
    Vote::factory()->create(['user_id' => $voter->id, 'model_type' => Item::class, 'model_id' => $item->id]);

    actingAs($viewer)
        ->get(route('public-user', $voter->username))
        ->assertDontSee('Hidden');
});

test('hides votes on comments on private items', function () {
    $viewer = User::factory()->create();
    $voter = User::factory()->create();

    $item = Item::factory()->create(['title' => 'Private Item', 'private' => true]);
    $comment = Comment::factory()->create(['item_id' => $item->id, 'content' => 'Hidden Comment']);

    Vote::factory()->create(['user_id' => $voter->id, 'model_type' => Comment::class, 'model_id' => $comment->id]);

    actingAs($viewer)
        ->get(route('public-user', $voter->username))
        ->assertDontSee('Private Item')
        ->assertDontSee('Hidden Comment');
});

test('counts only visible items', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();

    Item::factory()->count(2)->create(['user_id' => $author->id, 'private' => false]);
    Item::factory()->create(['user_id' => $author->id, 'private' => true]);

    $response = actingAs($viewer)->get(route('public-user', $author->username));

    expect($response->viewData('data')['items_created'])->toBe(2);
});

test('counts only visible comments', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();

    $publicItem = Item::factory()->create(['private' => false]);
    $privateItem = Item::factory()->create(['private' => true]);

    Comment::factory()->count(2)->create(['user_id' => $author->id, 'item_id' => $publicItem->id]);
    Comment::factory()->create(['user_id' => $author->id, 'item_id' => $privateItem->id]);

    $response = actingAs($viewer)->get(route('public-user', $author->username));

    expect($response->viewData('data')['comments_created'])->toBe(2);
});

test('activities limited to 20 items', function () {
    $viewer = User::factory()->create();
    $author = User::factory()->create();

    Item::factory()->count(25)->create(['user_id' => $author->id]);

    $activities = actingAs($viewer)
        ->get(route('public-user', $author->username))
        ->viewData('data')['activities'];

    expect($activities)->toHaveCount(20);
});
