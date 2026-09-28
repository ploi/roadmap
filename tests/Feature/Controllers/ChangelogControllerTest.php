<?php

use App\Models\Tag;
use App\Models\Item;
use App\Models\Project;
use App\Models\Changelog;
use App\Livewire\Changelog\Vote;
use App\Livewire\Changelog\Index;
use function Pest\Laravel\get;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Factories\Sequence;

beforeEach(function () {
    GeneralSettings::fake([
        'enable_changelog' => true,
        'show_changelog_author' => true,
        'show_changelog_related_items' => true
    ]);
});

test('changelog will show published changelog records in correct order', function () {
    $changelogs = Changelog::factory(3)->for(createUser())->state(new Sequence(
        ['published_at' => '2022-06-25 14:00:00'],
        ['published_at' => '2022-06-24 12:00:00'],
        ['published_at' => '2022-06-24 00:00:00'],
    ))->create();

    get(route('changelog'))
        ->assertSeeTextInOrder($changelogs->pluck('title')->toArray());
});

test('changelog is ordered by publish date instead of creation date', function () {
    Changelog::factory(3)->for(createUser())->state(new Sequence(
        ['title' => 'Published second', 'published_at' => '2022-06-24 12:00:00', 'created_at' => '2022-06-20 00:00:00'],
        ['title' => 'Published first', 'published_at' => '2022-06-23 12:00:00', 'created_at' => '2022-06-21 00:00:00'],
        ['title' => 'Published last', 'published_at' => '2022-06-25 12:00:00', 'created_at' => '2022-06-22 00:00:00'],
    ))->create();

    get(route('changelog'))
        ->assertSeeTextInOrder(['Published last', 'Published second', 'Published first']);
});

test('changelog records with the same publish date show the most recently added first', function () {
    Changelog::factory(2)->for(createUser())->state(new Sequence(
        ['title' => 'Added first', 'published_at' => '2022-06-24 12:00:00'],
        ['title' => 'Added second', 'published_at' => '2022-06-24 12:00:00'],
    ))->create();

    get(route('changelog'))
        ->assertSeeTextInOrder(['Added second', 'Added first']);
});

test('changelog overview only shows the first paragraph with a read more link', function () {
    Changelog::factory()->published()->for(createUser())->create([
        'content' => "First paragraph of the change.\n\nSecond paragraph of the change.",
    ]);

    get(route('changelog'))
        ->assertSeeText('First paragraph of the change.')
        ->assertDontSeeText('Second paragraph of the change.')
        ->assertSeeText(trans('changelog.read-more'));
});

test('changelog overview has no read more link when the content is a single paragraph', function () {
    Changelog::factory()->published()->for(createUser())->create([
        'content' => 'The only paragraph of the change.',
    ]);

    get(route('changelog'))
        ->assertSeeText('The only paragraph of the change.')
        ->assertDontSeeText(trans('changelog.read-more'));
});

test('changelog details show the full content', function () {
    $changelog = Changelog::factory()->published()->for(createUser())->create([
        'content' => "First paragraph of the change.\n\nSecond paragraph of the change.",
    ]);

    get(route('changelog.show', $changelog))
        ->assertSeeText('First paragraph of the change.')
        ->assertSeeText('Second paragraph of the change.');
});

test('changelog can be filtered by project', function () {
    $project = Project::factory()->create();
    Changelog::factory()->published()->for(createUser())->for($project)->create(['title' => 'Change in project']);
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create(['title' => 'Change in other project']);
    Changelog::factory()->published()->for(createUser())->create(['title' => 'Change without project']);

    get(route('changelog.show', $project))
        ->assertSeeText('Change in project')
        ->assertDontSeeText('Change in other project')
        ->assertDontSeeText('Change without project');
});

test('changelog can be filtered on changelogs without a project', function () {
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create(['title' => 'Change in project']);
    Changelog::factory()->published()->for(createUser())->create(['title' => 'Change without project']);

    get(route('changelog', ['project' => 'none']))
        ->assertSeeText('Change without project')
        ->assertDontSeeText('Change in project');
});

test('changelog filter is hidden when no changelog has a project', function () {
    Changelog::factory()->published()->for(createUser())->create();

    get(route('changelog'))
        ->assertDontSee('aria-current="page"', false);
});

test('changelog filter offers the without project option when such changelogs exist', function () {
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create();
    Changelog::factory()->published()->for(createUser())->create();

    get(route('changelog'))
        ->assertSee(route('changelog', ['project' => 'none']), false);
});

test('changelog filter does not offer the without project option when every changelog has a project', function () {
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create();

    get(route('changelog'))
        ->assertDontSee(route('changelog', ['project' => 'none']), false);
});

test('changelog filter does not offer projects that only have unpublished changelogs', function () {
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create();
    $project = Project::factory()->create();
    Changelog::factory()->for(createUser())->for($project)->create(['published_at' => now()->addDay()]);

    get(route('changelog'))
        ->assertDontSee(route('changelog.show', $project), false);
});

test('changelog filter hides private projects from non-members', function () {
    $project = Project::factory()->private()->create();
    Changelog::factory()->published()->for(createUser())->for($project)->create();
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create();

    get(route('changelog'))
        ->assertDontSee(route('changelog.show', $project), false);
});

test('changelog filter returns a 404 for a private project when not a member', function () {
    $project = Project::factory()->private()->create();
    Changelog::factory()->published()->for(createUser())->for($project)->create();

    get(route('changelog.show', $project))
        ->assertNotFound();
});

test('changelog slug takes precedence over a project with the same slug', function () {
    $changelog = Changelog::factory()->published()->for(createUser())->create(['title' => 'The changelog', 'slug' => 'shared-slug']);
    Changelog::factory()->published()->for(createUser())->for(Project::factory()->state(['slug' => 'shared-slug']))->create(['title' => 'Change in project']);

    get(route('changelog.show', $changelog))
        ->assertSeeText('The changelog')
        ->assertDontSeeText('Change in project');
});

test('changelog details return a 404 for a changelog without publish date', function () {
    $changelog = Changelog::factory()->for(createUser())->create(['published_at' => null]);

    get(route('changelog.show', $changelog))
        ->assertNotFound();
});

test('changelog filter returns a 404 for an unknown project', function () {
    Changelog::factory()->published()->for(createUser())->for(Project::factory())->create();

    get(route('changelog.show', 'unknown-project'))
        ->assertNotFound();
});

test('changelog details show the connected project in the breadcrumbs', function () {
    $project = Project::factory()->create();
    $changelog = Changelog::factory()->published()->for(createUser())->for($project)->create();

    get(route('changelog.show', $changelog))
        ->assertSee(route('changelog.show', $project), false);
});

test('changelog shows the requested page', function () {
    Changelog::factory()->for(createUser())->create(['title' => 'Newest change', 'published_at' => now()->subDay()]);
    Changelog::factory(Index::PER_PAGE - 1)->for(createUser())->create(['published_at' => now()->subDays(2)]);
    Changelog::factory()->for(createUser())->create(['title' => 'Oldest change', 'published_at' => now()->subWeek()]);

    get(route('changelog', ['page' => 2]))
        ->assertSeeText('Oldest change')
        ->assertDontSeeText('Newest change');
});

test('changelog will not show unpublished changelog records', function () {
    $changelogs = Changelog::factory(2)->for(createUser())->state(new Sequence(
        ['published_at' => today()->addDay()],
        ['published_at' => null],
    ))->create();

    get(route('changelog'))
        ->assertDontSeeText($changelogs->pluck('title')->toArray());
});

test('author is not visible when disabled in settings', function (bool $shouldBeVisible) {
    GeneralSettings::fake(['enable_changelog' => true, 'show_changelog_author' => $shouldBeVisible]);

    $user = createUser();
    Changelog::factory()->published()->for($user)->create();

    get(route('changelog'))
        ->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}($user->name);
})->with([
    'enabled' => [true],
    'disabled' => [false],
]);

test('related items are not visible when disabled in settings', function (bool $shouldBeVisible) {
    GeneralSettings::fake(['enable_changelog' => true, 'show_changelog_related_items' => $shouldBeVisible]);

    $item = Item::factory()->create();
    $changelog = Changelog::factory()->published()->for(createUser())->hasAttached($item)->create();

    get(route('changelog.show', $changelog))
        ->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}($item->title);
})->with([
    'enabled' => [true],
    'disabled' => [false],
]);

test('included items do not show private items', function () {
    $item = Item::factory()->private()->create(['title' => 'Private item']);
    $changelog = Changelog::factory()->published()->for(createUser())->hasAttached($item)->create();

    get(route('changelog.show', $changelog))
        ->assertDontSeeText('Private item');
});

test('included items are grouped under their changelog tag', function () {
    $tag = Tag::forceCreate(['name' => 'New features', 'slug' => 'new-features', 'changelog' => true]);
    $taggedItem = Item::factory()->create(['title' => 'Tagged item']);
    $taggedItem->tags()->attach($tag);
    $untaggedItem = Item::factory()->create(['title' => 'Untagged item']);
    $changelog = Changelog::factory()->published()->for(createUser())->hasAttached([$taggedItem, $untaggedItem])->create();

    get(route('changelog.show', $changelog))
        ->assertSeeTextInOrder(['Untagged item', 'New features', 'Tagged item']);
});

test('included items only list items of the changelog under a changelog tag', function () {
    $tag = Tag::forceCreate(['name' => 'New features', 'slug' => 'new-features', 'changelog' => true]);
    $includedItem = Item::factory()->create(['title' => 'Included item']);
    $otherItem = Item::factory()->create(['title' => 'Item of another changelog']);
    $includedItem->tags()->attach($tag);
    $otherItem->tags()->attach($tag);
    $changelog = Changelog::factory()->published()->for(createUser())->hasAttached($includedItem)->create();

    get(route('changelog.show', $changelog))
        ->assertSeeText('Included item')
        ->assertDontSeeText('Item of another changelog');
});

test('changelog details will show only details of one changelog record', function () {
    $changelogs = Changelog::factory(2)->published()->for(createUser())->create();

    get(route('changelog.show', $changelogs->first()))
        ->assertSeeText($changelogs->first()->title)
        ->assertDontSeeText($changelogs->skip(1)->first()->title);
});

test('changelog like button is not visible when disabled in settings', function (bool $shouldBeVisible) {
    GeneralSettings::fake(['enable_changelog' => true, 'show_changelog_like' => $shouldBeVisible]);

    $changelog = Changelog::factory()->published()->for(createUser())->create();

    get(route('changelog.show', $changelog))
        ->{$shouldBeVisible ? 'assertSeeLivewire' : 'assertDontSeeLivewire'}(Vote::class);
})->with([
    'enabled' => [true],
    'disabled' => [false],
]);

test('changelog is visible in navbar when enabled', function () {
    get('/')->assertSeeText(trans('changelog.changelog'));
});

test('changelog is not visible in navbar when disabled', function () {
    GeneralSettings::fake(['enable_changelog' => false]);

    get('/')->assertDontSeeText(trans('changelog.changelog'));
});

test('changelog will show a 404 when disabled in settings', function () {
    GeneralSettings::fake(['enable_changelog' => false]);

    get(route('changelog'))->assertNotFound();
});
