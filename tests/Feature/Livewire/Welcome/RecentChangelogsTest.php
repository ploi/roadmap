<?php

use App\Models\Project;
use Livewire\Livewire;
use App\Models\Changelog;
use function Pest\Laravel\get;
use App\Settings\GeneralSettings;
use App\Livewire\Welcome\RecentChangelogs;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('recent changelogs shows published changelogs by publish date', function () {
    Changelog::factory(3)->for(createUser())->state(new Sequence(
        ['title' => 'Older change', 'published_at' => now()->subWeek()],
        ['title' => 'Newer change', 'published_at' => now()->subDay()],
        ['title' => 'Unpublished change', 'published_at' => now()->addDay()],
    ))->create();

    Livewire::test(RecentChangelogs::class)
        ->assertSeeTextInOrder(['Newer change', 'Older change'])
        ->assertDontSeeText('Unpublished change');
});

test('recent changelogs links the project to its changelog page', function () {
    $project = Project::factory()->create(['title' => 'Connected project']);
    Changelog::factory()->published()->for(createUser())->for($project)->create();

    Livewire::test(RecentChangelogs::class)
        ->assertSeeText('Connected project')
        ->assertSee(route('changelog.show', $project), false);
});

test('recent changelogs hides a private project from non-members', function () {
    $project = Project::factory()->private()->create(['title' => 'Secret project']);
    Changelog::factory()->published()->for(createUser())->for($project)->create();

    Livewire::test(RecentChangelogs::class)
        ->assertDontSeeText('Secret project');
});

test('dashboard only shows recent changelogs when the changelog is enabled', function (bool $isChangelogEnabled) {
    GeneralSettings::fake([
        'enable_changelog' => $isChangelogEnabled,
        'dashboard_items' => [['type' => 'recent-changelogs', 'column_span' => 1]],
    ]);

    get(route('home'))
        ->{$isChangelogEnabled ? 'assertSeeLivewire' : 'assertDontSeeLivewire'}(RecentChangelogs::class);
})->with([
    'enabled' => [true],
    'disabled' => [false],
]);
