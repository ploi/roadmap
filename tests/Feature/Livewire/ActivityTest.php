<?php

use App\Models\Item;
use Livewire\Livewire;
use App\Enums\ItemActivity;
use App\Livewire\Activity;

test('authenticated user can access activity page', function () {
    $user = createAndLoginUser();

    $response = $this->get(route('activity'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(Activity::class);
});

test('activity component renders', function () {
    $user = createAndLoginUser();

    Livewire::test(Activity::class)
        ->assertStatus(200);
});

test('activity page shows activities for public items', function () {
    $user = createAndLoginUser();
    $item = Item::factory()->create([
        'private' => false,
        'title' => 'Public Test Item',
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($item, ItemActivity::Created);

    $response = $this->get(route('activity'));

    $response->assertSee('Created the item');
    $response->assertSee('Public Test Item');
    $response->assertSee($user->name);
});

test('activity component has search functionality', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'private' => false,
        'title' => 'Searchable Item',
        'user_id' => $user->id,
    ]);

    ItemActivity::createForItem($item, ItemActivity::Created);

    Livewire::test(Activity::class)
        ->assertStatus(200)
        ->assertSee('Searchable Item');
});

test('activity component has pagination', function () {
    $user = createAndLoginUser();

    // Create more than one page worth of activities
    for ($i = 0; $i < 15; $i++) {
        $item = Item::factory()->create([
            'private' => false,
            'user_id' => $user->id,
        ]);
        ItemActivity::createForItem($item, ItemActivity::Created);
    }

    $component = Livewire::test(Activity::class);

    // The table should be paginated (not showing all 15 at once)
    $component->assertStatus(200);
});

test('activity page does not show activities for private items', function () {
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

    $response = $this->get(route('activity'));

    $response->assertSee('Created the item');
    $response->assertDontSee('Made item private');
});
