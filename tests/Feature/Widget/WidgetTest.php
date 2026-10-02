<?php

use App\Models\Item;
use App\Models\Comment;
use App\Models\Project;
use App\Enums\ItemActivity;
use App\Settings\WidgetSettings;
use App\Settings\ActivityWidgetSettings;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->settings = app(WidgetSettings::class);
    $this->settings->enabled = true;
    $this->settings->position = 'bottom-right';
    $this->settings->primary_color = '#2563EB';
    $this->settings->button_text = 'Feedback';
    $this->settings->allowed_domains = [];
    $this->settings->save();
});

test('activity widget excludes private project items and private note counts', function () {
    $activitySettings = app(ActivityWidgetSettings::class);
    $activitySettings->enabled = true;
    $activitySettings->allowed_domains = [];
    $activitySettings->save();

    $user = createUser();
    $this->actingAs($user);
    $privateProject = Project::factory()->private()->create();
    $privateProjectItem = Item::factory()->create([
        'project_id' => $privateProject->id,
        'title' => 'Private Project Widget Activity',
        'user_id' => $user->id,
    ]);
    $publicItem = Item::factory()->create([
        'title' => 'Public Widget Activity',
        'user_id' => $user->id,
    ]);

    Comment::factory()->create(['item_id' => $publicItem->id, 'private' => false]);
    Comment::factory()->create(['item_id' => $publicItem->id, 'private' => true]);
    ItemActivity::createForItem($privateProjectItem, ItemActivity::Created);
    ItemActivity::createForItem($publicItem, ItemActivity::Created);

    $response = $this->getJson('/api/activity-widget/activities');

    $response->assertSuccessful();

    expect(json_encode($response->json(), JSON_THROW_ON_ERROR))
        ->not->toContain('Private Project Widget Activity');

    $publicActivity = collect($response->json('activities'))
        ->first(fn (array $activity) => str_contains($activity['description'], 'Public Widget Activity'));

    expect($publicActivity)->not->toBeNull()
        ->and($publicActivity['comments'])->toBe(1);
});

test('widget config endpoint returns configuration when enabled', function () {
    $response = $this->getJson('/api/widget/config');

    $response->assertSuccessful()
        ->assertJson([
            'enabled' => true,
            'position' => 'bottom-right',
            'primary_color' => '#2563EB',
            'button_text' => 'Feedback',
        ]);
});

test('widget config endpoint returns disabled when widget is disabled', function () {
    $this->settings->enabled = false;
    $this->settings->save();

    $response = $this->getJson('/api/widget/config');

    $response->assertSuccessful()
        ->assertJson([
            'enabled' => false,
        ]);
});

test('widget can submit feedback successfully', function () {
    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback from the widget',
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Feedback submitted successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'item_id',
            'item_url',
        ]);

    assertDatabaseHas(Item::class, [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback from the widget',
    ]);

    expect($response->json('item_url'))->toContain('/items/');
});

test('widget can submit feedback anonymously without email', function () {
    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Anonymous Feedback',
        'content' => 'This is anonymous feedback',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Feedback submitted successfully',
        ]);

    assertDatabaseHas(Item::class, [
        'title' => 'Anonymous Feedback',
        'content' => 'This is anonymous feedback',
        'user_id' => null,
    ]);
});

test('widget submission requires title and content', function () {
    $response = $this->postJson('/api/widget/submit', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content']);
});

test('widget submission fails when widget is disabled', function () {
    $this->settings->enabled = false;
    $this->settings->save();

    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback',
    ]);

    $response->assertForbidden();
});

test('widget submission respects domain restrictions', function () {
    $this->settings->allowed_domains = ['example.com'];
    $this->settings->save();

    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback',
    ], [
        'Origin' => 'https://notallowed.com',
    ]);

    $response->assertForbidden();
});

test('widget submission allows configured domains', function () {
    $this->settings->allowed_domains = ['example.com'];
    $this->settings->save();

    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback',
    ], [
        'Origin' => 'https://example.com',
    ]);

    $response->assertCreated();
});

test('widget config respects domain restrictions', function () {
    $this->settings->allowed_domains = ['example.com'];
    $this->settings->save();

    // Disallowed domain
    $response = $this->getJson('/api/widget/config', [
        'Origin' => 'https://notallowed.com',
    ]);

    $response->assertSuccessful()
        ->assertJson(['enabled' => false]);

    // Allowed domain
    $response = $this->getJson('/api/widget/config', [
        'Origin' => 'https://example.com',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'enabled' => true,
            'position' => 'bottom-right',
        ]);
});

test('widget javascript is served correctly', function () {
    $response = $this->get('/widget.js');

    $response->assertSuccessful()
        ->assertHeader('Content-Type', 'application/javascript');

    expect($response->content())
        ->toContain('RoadmapWidgetElement')
        ->toContain('customElements.define')
        ->toContain('roadmap-widget');
});

test('widget javascript includes dark mode support', function () {
    $response = $this->get('/widget.js');

    $response->assertSuccessful();

    expect($response->content())
        ->toContain('setupDarkModeObserver')
        ->toContain('updateDarkMode')
        ->toContain('this.darkMode')
        ->toContain("document.documentElement.classList.contains('dark')");
});

test('widget submission automatically upvotes item for user', function () {
    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback with Vote',
        'content' => 'This feedback should have an automatic upvote',
        'email' => 'voter@example.com',
        'name' => 'Voter User',
    ]);

    $response->assertCreated();

    $item = Item::where('title', 'Test Feedback with Vote')->first();

    expect($item)->not->toBeNull()
        ->and($item->votes()->count())->toBe(1)
        ->and($item->votes()->first()->user->email)->toBe('voter@example.com');
});

test('widget submission creates activity log with correct user', function () {
    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Activity Log',
        'content' => 'This should have correct user in activity log',
        'email' => 'activity@example.com',
        'name' => 'Activity User',
    ]);

    $response->assertCreated();

    $item = Item::where('title', 'Test Activity Log')->first();
    $activity = $item->activities()->first();

    expect($item)->not->toBeNull()
        ->and($activity)->not->toBeNull()
        ->and($activity->causer)->not->toBeNull()
        ->and($activity->causer->email)->toBe('activity@example.com')
        ->and($activity->causer->name)->toBe('Activity User');
});
