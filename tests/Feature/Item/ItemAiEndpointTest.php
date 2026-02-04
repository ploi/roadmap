<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Project;

beforeEach(function () {
    Item::unsetEventDispatcher();

    $this->user = createUser();
    $this->project = Project::factory()->create();
    $this->board = Board::factory()->create(['project_id' => $this->project->id]);
    $this->item = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->user,
    ]);
});

test('it returns item data as json by default', function () {
    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
    ]));

    $response->assertOk()
        ->assertJsonStructure(['title', 'content', 'board', 'project', 'votes', 'tags'])
        ->assertJsonMissing(['comments' => []])
        ->assertJson([
            'title' => $this->item->title,
            'content' => $this->item->content,
            'board' => $this->board->title,
            'project' => $this->project->title,
        ]);
});

test('it excludes comments by default', function () {
    Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => false,
    ]);

    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
    ]));

    $response->assertOk()
        ->assertJsonMissingPath('comments');
});

test('it includes public comments when requested via include[comments]=1', function () {
    $comment = Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => false,
    ]);

    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'include' => ['comments' => 1],
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'comments')
        ->assertJsonPath('comments.0.content', $comment->content);
});

test('it excludes private comments even when comments are included', function () {
    Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => true,
    ]);

    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'include' => ['comments' => 1],
    ]));

    $response->assertOk()
        ->assertJsonCount(0, 'comments');
});

test('it returns yaml when format=yml', function () {
    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'format' => 'yml',
    ]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/yaml; charset=utf-8');

    expect($response->getContent())->toContain('title:');
});

test('it returns markdown when format=markdown', function () {
    $response = $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'format' => 'markdown',
    ]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=utf-8');

    expect($response->getContent())->toContain("# {$this->item->title}");
});

test('it returns 404 for private items', function () {
    $item = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->user,
        'private' => true,
    ]);

    $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $item->slug,
    ]))->assertNotFound();
});
