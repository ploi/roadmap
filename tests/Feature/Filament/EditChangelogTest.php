<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Changelog;
use App\Settings\GeneralSettings;
use App\Filament\Resources\Changelogs\Pages\EditChangelog;

test('admin can connect a project to a changelog', function () {
    GeneralSettings::fake(['enable_changelog' => true]);
    createAndLoginUser(['role' => UserRole::Admin]);
    $changelog = Changelog::factory()->published()->for(createUser())->create();
    $project = Project::factory()->create();

    Livewire::test(EditChangelog::class, ['record' => $changelog->getRouteKey()])
        ->fillForm(['project_id' => $project->id])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($changelog->refresh()->project_id)->toBe($project->id);
});
