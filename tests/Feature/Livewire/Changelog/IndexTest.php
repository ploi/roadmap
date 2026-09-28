<?php

use Livewire\Livewire;
use App\Models\Project;
use App\Models\Changelog;
use App\Livewire\Changelog\Index;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('changelog shows the connected project with a link to it', function () {
    $project = Project::factory()->create(['title' => 'Connected project']);
    Changelog::factory()->published()->for(createUser())->for($project)->create();

    Livewire::test(Index::class)
        ->assertSeeText('Connected project')
        ->assertSee(route('projects.show', $project), false);
});

test('changelog without a connected project does not link to a project', function () {
    Changelog::factory()->published()->for(createUser())->create();

    Livewire::test(Index::class)
        ->assertDontSee('/projects/', false);
});

test('changelog hides a connected private project from non-members', function () {
    $project = Project::factory()->private()->create(['title' => 'Secret project']);
    Changelog::factory()->published()->for(createUser())->for($project)->create();
    createAndLoginUser();

    Livewire::test(Index::class)
        ->assertDontSeeText('Secret project');
});

test('changelog shows a connected private project to its members', function () {
    $project = Project::factory()->private()->create(['title' => 'Secret project']);
    Changelog::factory()->published()->for(createUser())->for($project)->create();
    $project->members()->attach(createAndLoginUser());

    Livewire::test(Index::class)
        ->assertSeeText('Secret project');
});

test('changelog shows one page of changelogs with a link to the next page', function () {
    Changelog::factory(Index::PER_PAGE)->for(createUser())->create(['published_at' => now()->subDay()]);
    Changelog::factory()->for(createUser())->create(['title' => 'Oldest change', 'published_at' => now()->subWeek()]);

    Livewire::test(Index::class)
        ->assertDontSeeText('Oldest change')
        ->assertSee(route('changelog', ['page' => 2]), false);
});

test('changelog loads the next page when scrolling down', function () {
    Changelog::factory(Index::PER_PAGE)->for(createUser())->create(['published_at' => now()->subDay()]);
    Changelog::factory()->for(createUser())->create(['title' => 'Oldest change', 'published_at' => now()->subWeek()]);

    Livewire::test(Index::class)
        ->call('loadMore')
        ->assertSeeText('Oldest change')
        ->assertDontSee(route('changelog', ['page' => 3]), false);
});

test('changelog has no link to a next page when all changelogs are shown', function () {
    Changelog::factory(Index::PER_PAGE)->for(createUser())->create(['published_at' => now()->subDay()]);

    Livewire::test(Index::class)
        ->assertDontSee(route('changelog', ['page' => 2]), false);
});

test('changelog starting on a later page links back to the newer changes', function () {
    Changelog::factory()->for(createUser())->create(['title' => 'Newest change', 'published_at' => now()->subDay()]);
    Changelog::factory(Index::PER_PAGE - 1)->for(createUser())->create(['published_at' => now()->subDays(2)]);
    Changelog::factory()->for(createUser())->create(['title' => 'Oldest change', 'published_at' => now()->subWeek()]);

    Livewire::test(Index::class, ['firstPage' => 2])
        ->assertSeeText('Oldest change')
        ->assertDontSeeText('Newest change')
        ->assertSeeText(trans('changelog.newer-changes'));
});

test('changelog keeps the project filter when loading more', function () {
    $project = Project::factory()->create();
    Changelog::factory(Index::PER_PAGE)->for(createUser())->for($project)->create(['published_at' => now()->subDay()]);
    Changelog::factory()->for(createUser())->for($project)->create(['title' => 'Oldest project change', 'published_at' => now()->subWeek()]);
    Changelog::factory()->for(createUser())->create(['title' => 'Change without project', 'published_at' => now()->subWeek()]);

    Livewire::test(Index::class, ['projectFilter' => $project->slug])
        ->call('loadMore')
        ->assertSeeText('Oldest project change')
        ->assertDontSeeText('Change without project');
});

test('changelog filtered on a project links to the next page of that project', function () {
    $project = Project::factory()->create();
    Changelog::factory(Index::PER_PAGE + 1)->for(createUser())->for($project)->create(['published_at' => now()->subDay()]);

    Livewire::test(Index::class, ['projectFilter' => $project->slug])
        ->assertSee(route('changelog.show', ['slug' => $project->slug, 'page' => 2]), false);
});

test('changelog groups changelogs by day by default', function () {
    GeneralSettings::fake(['group_changelog_by_month' => false]);
    Changelog::factory(2)->for(createUser())->state(new Sequence(
        ['published_at' => '2022-06-24 12:00:00'],
        ['published_at' => '2022-06-10 12:00:00'],
    ))->create();

    Livewire::test(Index::class)
        ->assertSeeTextInOrder(['June 24, 2022', 'June 10, 2022'])
        ->assertDontSeeText('June 2022');
});

test('changelog groups changelogs by month when enabled and shows the date per changelog', function () {
    GeneralSettings::fake(['group_changelog_by_month' => true]);
    Changelog::factory(2)->for(createUser())->state(new Sequence(
        ['published_at' => '2022-06-24 12:00:00'],
        ['published_at' => '2022-06-10 12:00:00'],
    ))->create();

    Livewire::test(Index::class)
        ->assertSeeTextInOrder(['June 2022', 'Jun 24, 2022', 'Jun 10, 2022'])
        ->assertDontSeeText('June 24, 2022');
});
