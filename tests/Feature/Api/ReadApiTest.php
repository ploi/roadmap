<?php

use App\Models\Item;
use App\Models\ApiToken;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Project;
use function Pest\Laravel\getJson;
use function Pest\Laravel\withToken;

function apiToken(): string
{
    return ApiToken::generate('Polder')[1];
}

it('requires a token', function () {
    getJson('/api/v1/items')->assertUnauthorized();
});

it('rejects an unknown token', function () {
    withToken('rm_unknown')->getJson('/api/v1/items')->assertUnauthorized();
});

it('rejects user tokens', function () {
    $token = createUser()->createToken('MCP')->plainTextToken;

    withToken($token)->getJson('/api/v1/items')->assertUnauthorized();
});

it('records when a token was last used', function () {
    [$apiToken, $plain] = ApiToken::generate('Polder');

    withToken($plain)->getJson('/api/v1/items')->assertOk();

    expect($apiToken->refresh()->last_used_at)->not->toBeNull();
});

it('lists projects, items and comments including private ones', function () {
    $project = Project::factory()->private()->create();
    $board = Board::factory()->create(['project_id' => $project->id]);
    $item = Item::factory()->create(['project_id' => $project->id, 'board_id' => $board->id, 'private' => true]);
    $comment = Comment::factory()->create(['item_id' => $item->id]);

    $token = apiToken();

    withToken($token)->getJson('/api/v1/projects')->assertOk()->assertJsonPath('data.0.id', $project->id);
    withToken($token)->getJson('/api/v1/items')->assertOk()
        ->assertJsonPath('data.0.id', $item->id)
        ->assertJsonPath('data.0.status', $board->title)
        ->assertJsonPath('data.0.private', true);
    withToken($token)->getJson('/api/v1/comments')->assertOk()->assertJsonPath('data.0.id', $comment->id);
});

it('paginates and filters on updated_since', function () {
    $old = Item::factory()->create();
    $old->forceFill(['updated_at' => now()->subDays(10)])->saveQuietly();
    $new = Item::factory()->create();

    $response = withToken(apiToken())
        ->getJson('/api/v1/items?per_page=1&updated_since=' . urlencode(now()->subDay()->toIso8601String()))
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.id'))->toBe($new->id)
        ->and($response->json('meta.total'))->toBe(1);
});

it('validates updated_since', function () {
    withToken(apiToken())->getJson('/api/v1/items?updated_since=nope')->assertUnprocessable();
});

it('converts the updated_since offset to the app timezone', function () {
    $item = Item::factory()->create();
    $item->forceFill(['updated_at' => '2026-11-02 11:00:00'])->saveQuietly();

    // 12:00+02:00 is 10:00 UTC, so an item updated at 11:00 UTC must be included.
    withToken(apiToken())
        ->getJson('/api/v1/items?updated_since=' . urlencode('2026-11-02T12:00:00+02:00'))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('falls back to the default page size for an empty per_page', function () {
    Item::factory()->count(3)->create();

    withToken(apiToken())->getJson('/api/v1/items?per_page=')->assertOk()->assertJsonPath('meta.per_page', 50);
});

it('answers with json errors without an Accept header', function () {
    $this->get('/api/v1/items')->assertUnauthorized()->assertJsonPath('message', 'Unauthenticated.');
    $this->withToken(apiToken())->get('/api/v1/items?updated_since=nope')->assertUnprocessable();
});
