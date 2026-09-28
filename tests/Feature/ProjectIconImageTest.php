<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Models\Project;
use function Pest\Laravel\get;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\Projects\Pages\EditProject;

beforeEach(function () {
    Storage::fake('public');
});

test('sidebar shows the uploaded icon image instead of the heroicon', function () {
    $project = Project::factory()->create([
        'icon' => 'heroicon-o-star',
        'icon_image' => 'project-icons/logo.svg',
    ]);

    get(route('projects.show', $project))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url('project-icons/logo.svg'), false);
});

test('sidebar falls back to the heroicon when no icon image is uploaded', function () {
    $project = Project::factory()->create(['icon' => 'heroicon-o-star']);

    get(route('projects.show', $project))
        ->assertOk()
        ->assertDontSee('project-icons/', false);
});

test('admin can upload an svg as project icon', function () {
    createAndLoginUser(['role' => UserRole::Admin]);
    $project = Project::factory()->create();

    Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
        ->fillForm([
            'icon_image' => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $project->refresh();

    expect($project->icon_image)->toStartWith('project-icons/');
    Storage::disk('public')->assertExists($project->icon_image);
});

test('icon image upload rejects non png or svg files', function () {
    createAndLoginUser(['role' => UserRole::Admin]);
    $project = Project::factory()->create();

    Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
        ->fillForm([
            'icon_image' => UploadedFile::fake()->image('logo.jpg'),
        ])
        ->call('save')
        ->assertHasFormErrors(['icon_image']);
});

test('replacing or deleting the project removes the old icon image', function () {
    Storage::disk('public')->put('project-icons/old.png', 'old');
    Storage::disk('public')->put('project-icons/new.png', 'new');

    $project = Project::factory()->create(['icon_image' => 'project-icons/old.png']);

    $project->update(['icon_image' => 'project-icons/new.png']);

    Storage::disk('public')->assertMissing('project-icons/old.png');
    Storage::disk('public')->assertExists('project-icons/new.png');

    $project->delete();

    Storage::disk('public')->assertMissing('project-icons/new.png');
});
