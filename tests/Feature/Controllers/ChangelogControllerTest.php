<?php

use App\Models\Item;
use App\Models\Changelog;
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
    Changelog::factory()->published()->for(createUser())->hasAttached($item)->create();

    get(route('changelog'))
        ->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}($item->title);
})->with([
    'enabled' => [true],
    'disabled' => [false],
]);

test('changelog details will show only details of one changelog record', function () {
    $changelogs = Changelog::factory(2)->published()->for(createUser())->create();

    get(route('changelog.show', $changelogs->first()))
        ->assertSeeText($changelogs->first()->title)
        ->assertDontSeeText($changelogs->skip(1)->first()->title);
});

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
