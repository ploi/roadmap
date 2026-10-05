<?php

use App\Models\Item;
use App\Models\User;
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

test('widget submission requires an origin when domains are restricted', function () {
    $this->settings->allowed_domains = ['example.com'];
    $this->settings->save();

    $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback',
        'content' => 'This is a test feedback',
    ])->assertForbidden();
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

test('widget config is disabled without an origin when domains are restricted', function () {
    $this->settings->allowed_domains = ['example.com'];
    $this->settings->save();

    $this->getJson('/api/widget/config')
        ->assertSuccessful()
        ->assertJson(['enabled' => false]);
});

test('activity widget requires an origin when domains are restricted', function () {
    $settings = app(ActivityWidgetSettings::class);
    $settings->enabled = true;
    $settings->allowed_domains = ['example.com'];
    $settings->save();

    $this->getJson('/api/activity-widget/config')
        ->assertSuccessful()
        ->assertJson(['enabled' => false]);

    $this->getJson('/api/activity-widget/activities')
        ->assertForbidden();
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

test('widget email does not assign an existing user or create a vote', function () {
    $user = User::factory()->create(['email' => 'voter@example.com']);

    $response = $this->postJson('/api/widget/submit', [
        'title' => 'Test Feedback with Vote',
        'content' => 'This feedback should have an automatic upvote',
        'email' => 'voter@example.com',
        'name' => 'Voter User',
    ]);

    $response->assertCreated();

    $item = Item::where('title', 'Test Feedback with Vote')->first();

    expect($item)->not->toBeNull()
        ->and($item->user_id)->toBeNull()
        ->and($item->votes()->count())->toBe(0)
        ->and(User::find($user->id))->not->toBeNull();
});

test('widget email does not assign an activity causer', function () {
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
        ->and($activity->causer)->toBeNull();
});
