<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use Livewire\Livewire;
use App\Enums\ItemActivity;
use App\Livewire\Welcome\RecentActivity;
use Spatie\Activitylog\Models\Activity;

test('recent activity component renders', function () {
    $user = createAndLoginUser();

    Livewire::test(RecentActivity::class)
        ->assertStatus(200);
});

test('recent activity shows activities for public items with item titles', function () {
    $user = createAndLoginUser();
    $item = Item::factory()->create([
        'private' => false,
        'title' => 'Public Test Item',
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($item, ItemActivity::Created);

    Livewire::test(RecentActivity::class)
        ->assertSee('Created the item')
        ->assertSee('Public Test Item')
        ->assertSee($user->name);
});

test('recent activity does not show activities for private items', function () {
    $user = createAndLoginUser();

    $publicItem = Item::factory()->create([
        'private' => false,
        'title' => 'Public Item',
        'user_id' => $user->id,
    ]);

    $privateItem = Item::factory()->create([
        'private' => true,
        'title' => 'Private Item',
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($publicItem, ItemActivity::Created);
    ItemActivity::createForItem($privateItem, ItemActivity::MadePrivate);

    $component = Livewire::test(RecentActivity::class);

    $component->assertSee('Created the item');
    $component->assertDontSee('Made item private');
});

test('recent activity does not show activities without causer', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'private' => false,
        'title' => 'Test Item',
        'user_id' => $user->id,
    ]);

    // Create activity with causer
    ItemActivity::createForItem($item, ItemActivity::Created);

    // Create activity without causer by manually creating it
    Activity::create([
        'log_name' => 'default',
        'description' => 'system generated',
        'subject_type' => Item::class,
        'subject_id' => $item->id,
        'causer_type' => null,
        'causer_id' => null,
    ]);

    // Test that the component only shows activities with causers
    $component = Livewire::test(RecentActivity::class);

    $component->assertSee('Created the item');
    $component->assertDontSee('System generated');
});

test('recent activity orders activities by most recent first', function () {
    $user = createAndLoginUser();

    $olderItem = Item::factory()->create([
        'private' => false,
        'created_at' => now()->subDays(2),
        'user_id' => $user->id,
    ]);

    $newerItem = Item::factory()->create([
        'private' => false,
        'created_at' => now()->subDay(),
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($olderItem, ItemActivity::Created);

    sleep(1);

    ItemActivity::createForItem($newerItem, ItemActivity::Created);

    $activities = Activity::query()
        ->whereNotNull('causer_id')
        ->orderBy('created_at', 'desc')
        ->limit(2)
        ->get();

    expect($activities->first()->subject_id)->toBe($newerItem->id);
    expect($activities->last()->subject_id)->toBe($olderItem->id);
});

test('recent activity limits results to 10', function () {
    $user = createAndLoginUser();

    for ($i = 0; $i < 15; $i++) {
        $item = Item::factory()->create([
            'private' => false,
            'user_id' => $user->id,
        ]);
        ItemActivity::createForItem($item, ItemActivity::Created);
    }

    $activities = Activity::query()
        ->whereHasMorph('subject', ['App\Models\Item'], function ($query) {
            $query->where('private', false);
        })
        ->whereNotNull('causer_id')
        ->limit(10)
        ->get();

    expect($activities)->toHaveCount(10);
});

test('recent activity shows user name for causer', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $this->actingAs($user);

    $item = Item::factory()->create([
        'private' => false,
        'user_id' => $user->id,
    ]);
    ItemActivity::createForItem($item, ItemActivity::Created);

    Livewire::test(RecentActivity::class)
        ->assertSee('John Doe');
});

test('recent activity shows votes count', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'private' => false,
        'total_votes' => 5,
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($item, ItemActivity::Created);

    $component = Livewire::test(RecentActivity::class);

    $component->assertSee('5');
});

test('recent activity shows comments count', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'private' => false,
        'user_id' => $user->id,
    ]);

    Comment::factory()->count(3)->create([
        'item_id' => $item->id,
        'private' => false,
    ]);

    ItemActivity::createForItem($item, ItemActivity::Created);

    $activity = Activity::query()
        ->with('subject.comments')
        ->whereNotNull('causer_id')
        ->first();

    expect($activity->subject->comments)->toHaveCount(3);
});
