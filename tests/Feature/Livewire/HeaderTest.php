<?php

use Livewire\Livewire;
use App\Models\Project;
use App\Livewire\Header;
use App\Settings\GeneralSettings;

beforeEach(function () {
    GeneralSettings::fake([
        'select_project_when_creating_item' => true,
        'select_board_when_creating_item' => true,
        'project_required_when_creating_item' => false,
        'board_required_when_creating_item' => false,
        'users_must_verify_email' => false,
    ]);
});

test('header component renders successfully', function () {
    $user = createAndLoginUser();

    Livewire::test(Header::class)
        ->assertStatus(200);
});

test('submit item action pre-fills project when currentProjectId is set', function () {
    $user = createAndLoginUser();
    $project = Project::factory()->create();

    // When we create an item with currentProjectId set, and we don't override the project_id,
    // it should use the pre-filled project_id
    Livewire::test(Header::class, ['currentProjectId' => $project->id])
        ->callAction('submitItem', data: [
            'title' => 'Test Feature with Pre-fill',
            'content' => 'This should use the pre-filled project.',
            // Not explicitly providing project_id - should use pre-filled value
        ])
        ->assertHasNoFormErrors();

    // The item should be created with the pre-filled project
    $this->assertDatabaseHas('items', [
        'title' => 'Test Feature with Pre-fill',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);
});

test('submit item action does not pre-fill project when currentProjectId is null', function () {
    $user = createAndLoginUser();

    $component = Livewire::test(Header::class, ['currentProjectId' => null]);

    // Mount the action
    $component->mountAction('submitItem');

    // Assert that the project_id is not set (or is null)
    $component->assertFormSet([
        'project_id' => null,
    ]);
});

test('submit item action shows login notification for guest users', function () {
    // Test without logging in
    Livewire::test(Header::class)
        ->callAction('submitItem')
        ->assertNotified();
});

test('user can create item with pre-filled project', function () {
    $user = createAndLoginUser();
    $project = Project::factory()->create();

    Livewire::test(Header::class, ['currentProjectId' => $project->id])
        ->callAction('submitItem', data: [
            'title' => 'Test Feature Request',
            'content' => 'This is a test content for the feature request.',
            'project_id' => $project->id,
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('items', [
        'title' => 'Test Feature Request',
        'content' => 'This is a test content for the feature request.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);
});

test('user can override pre-filled project with different project', function () {
    $user = createAndLoginUser();
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    Livewire::test(Header::class, ['currentProjectId' => $project1->id])
        ->callAction('submitItem', data: [
            'title' => 'Test Feature Request',
            'content' => 'This is a test content for the feature request.',
            'project_id' => $project2->id, // Override with different project
        ])
        ->assertHasNoFormErrors();

    // Assert that the item was created with project2, not project1
    $this->assertDatabaseHas('items', [
        'title' => 'Test Feature Request',
        'project_id' => $project2->id,
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseMissing('items', [
        'title' => 'Test Feature Request',
        'project_id' => $project1->id,
    ]);
});

test('project field is shown when select_project_when_creating_item setting is enabled', function () {
    GeneralSettings::fake([
        'select_project_when_creating_item' => true,
    ]);

    $user = createAndLoginUser();

    $component = Livewire::test(Header::class);

    $component->mountAction('submitItem');

    // Assert that the form has the project_id field
    $component->assertFormFieldExists('project_id');
});

test('project field is hidden when select_project_when_creating_item setting is disabled', function () {
    GeneralSettings::fake([
        'select_project_when_creating_item' => false,
    ]);

    $user = createAndLoginUser();

    $component = Livewire::test(Header::class);

    $component->mountAction('submitItem');

    // Assert that the form does not have the project_id field
    $component->assertFormFieldDoesNotExist('project_id');
});

test('project field is required when project_required_when_creating_item is true', function () {
    GeneralSettings::fake([
        'select_project_when_creating_item' => true,
        'project_required_when_creating_item' => true,
    ]);

    $user = createAndLoginUser();

    Livewire::test(Header::class)
        ->callAction('submitItem', data: [
            'title' => 'Test Feature Request',
            'content' => 'This is a test content for the feature request.',
            // Intentionally not providing project_id
        ])
        ->assertHasFormErrors(['project_id']);
});

test('project field is optional when project_required_when_creating_item is false', function () {
    GeneralSettings::fake([
        'select_project_when_creating_item' => true,
        'project_required_when_creating_item' => false,
    ]);

    $user = createAndLoginUser();

    Livewire::test(Header::class)
        ->callAction('submitItem', data: [
            'title' => 'Test Feature Request',
            'content' => 'This is a test content for the feature request.',
            // Not providing project_id - should be valid
        ])
        ->assertHasNoFormErrors();
});
