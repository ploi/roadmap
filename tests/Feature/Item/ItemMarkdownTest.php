<?php

use App\Models\Item;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Project;
use App\Settings\GeneralSettings;

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

test('it returns the item as markdown on the .md url', function () {
    $response = $this->get("/projects/{$this->project->slug}/items/{$this->item->slug}.md");

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
        ->assertHeader('Vary', 'Accept')
        ->assertHeader('X-Robots-Tag', 'noindex');

    expect($response->getContent())
        ->toContain("# {$this->item->title}")
        ->toContain("**Project:** {$this->project->title}")
        ->toContain("**Board:** {$this->board->title}")
        ->toContain($this->item->content);
});

test('it returns markdown on the item url when the client prefers text/markdown', function () {
    $response = $this->get($this->item->view_url, ['Accept' => 'text/markdown']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

    expect($response->getContent())->toContain("# {$this->item->title}");
});

test('it returns html on the item url for browsers', function () {
    $this->get($this->item->view_url, ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'])
        ->assertOk()
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
        ->assertHeader('Vary', 'Accept')
        ->assertSee('<link rel="alternate" type="text/markdown" href="' . $this->item->markdown_url . '"', false)
        ->assertSee(trans('items.copy-for-llms'));
});

test('it returns markdown for items without a project', function () {
    $item = Item::factory()->create([
        'project_id' => null,
        'board_id' => null,
        'user_id' => $this->user,
    ]);

    expect($item->markdown_url)->toBe(route('items.markdown', $item->slug));

    $response = $this->get($item->markdown_url)->assertOk();

    expect($response->getContent())
        ->toContain("# {$item->title}")
        ->not->toContain('**Project:**');
});

test('it redirects the project-less .md url to the project .md url when the item has a project', function () {
    $this->get(route('items.markdown', $this->item->slug))
        ->assertRedirect($this->item->markdown_url);
});

test('it excludes comments by default', function () {
    Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => false,
    ]);

    $response = $this->get($this->item->markdown_url)->assertOk();

    expect($response->getContent())->not->toContain('## Comments');
});

test('it includes public comments when requested via include[comments]=1', function () {
    $comment = Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => false,
    ]);

    $response = $this->get($this->item->markdown_url . '?include[comments]=1')->assertOk();

    expect($response->getContent())
        ->toContain('## Comments')
        ->toContain($comment->content);
});

test('it ignores a malformed include query', function () {
    $this->get($this->item->markdown_url . '?include=comments')->assertOk();
});

test('it excludes private comments even when comments are included', function () {
    $comment = Comment::factory()->create([
        'item_id' => $this->item->id,
        'user_id' => $this->user->id,
        'private' => true,
    ]);

    $response = $this->get($this->item->markdown_url . '?include[comments]=1')->assertOk();

    expect($response->getContent())->not->toContain($comment->content);
});

test('it returns 404 for private items', function () {
    $item = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->user,
        'private' => true,
    ]);

    $this->get(route('projects.items.markdown', [$this->project->slug, $item->slug]))->assertNotFound();
    $this->get(route('projects.items.show', [$this->project->slug, $item->slug]), ['Accept' => 'text/markdown'])->assertNotFound();
});

test('the old ai endpoint permanently redirects to the .md url', function () {
    $this->get(route('projects.items.ai', [$this->project->slug, $this->item->slug]))
        ->assertStatus(301)
        ->assertRedirect($this->item->markdown_url);
});

test('the old ai endpoint keeps the include query when redirecting', function () {
    $this->get(route('projects.items.ai', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'format' => 'json',
        'include' => ['comments' => 1],
    ]))->assertRedirect(route('projects.items.markdown', [
        'project' => $this->project->slug,
        'item' => $this->item->slug,
        'include' => ['comments' => 1],
    ]));
});

test('it hides the ask ai menu for private items', function () {
    $this->item->update(['private' => true]);

    $this->actingAs(createUser(['role' => UserRole::Admin]))
        ->get($this->item->view_url)
        ->assertOk()
        ->assertSee(trans('items.copy-for-llms'))
        ->assertDontSee('https://claude.ai/new', false);
});

test('it shows the ask ai menu for public items', function () {
    $this->get($this->item->view_url)
        ->assertOk()
        ->assertSee('https://claude.ai/new?q=' . urlencode(trans('items.ask-ai-prompt', ['url' => $this->item->markdown_url])), false);
});

test('it hides the ask ai menu when the roadmap is password protected', function () {
    GeneralSettings::fake(['password' => 'secret']);

    $this->withSession(['password-login-authorized' => true])
        ->get($this->item->view_url)
        ->assertOk()
        ->assertDontSee('https://claude.ai/new', false);
});
