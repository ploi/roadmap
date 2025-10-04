<?php

use App\Models\Item;
use App\Settings\WidgetSettings;
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
