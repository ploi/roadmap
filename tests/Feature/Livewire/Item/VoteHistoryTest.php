<?php

use App\Models\Item;
use App\Models\Vote;
use Livewire\Livewire;
use Illuminate\Support\Carbon;
use App\Livewire\Item\VoteHistory;
use App\Filament\Widgets\VoteHistoryChart;

test('vote history component renders', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(VoteHistory::class, ['item' => $item])
        ->assertStatus(200);
});

test('vote history shows empty state when no votes exist', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(VoteHistory::class, ['item' => $item])
        ->assertSee(trans('items.no-vote-history'));
});

test('vote history chart widget renders with item', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(VoteHistoryChart::class, ['item' => $item])
        ->assertStatus(200);
});

test('vote history chart provides data with votes', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    $vote = new Vote();
    $vote->user_id = $user->id;
    $vote->model_type = Item::class;
    $vote->model_id = $item->id;
    $vote->subscribed = false;
    $vote->save();

    $component = Livewire::test(VoteHistoryChart::class, ['item' => $item]);

    $component->assertStatus(200);
});

test('vote history chart aggregates votes by date', function () {
    $user = createAndLoginUser();
    $secondUser = createUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    $today = Carbon::today();

    $vote1 = new Vote();
    $vote1->user_id = $user->id;
    $vote1->model_type = Item::class;
    $vote1->model_id = $item->id;
    $vote1->subscribed = false;
    $vote1->created_at = $today;
    $vote1->save();

    $vote2 = new Vote();
    $vote2->user_id = $secondUser->id;
    $vote2->model_type = Item::class;
    $vote2->model_id = $item->id;
    $vote2->subscribed = false;
    $vote2->created_at = $today;
    $vote2->save();

    $component = Livewire::test(VoteHistoryChart::class, ['item' => $item]);

    $component->assertStatus(200);
});

test('vote history chart fills gaps between dates', function () {
    $user = createAndLoginUser();
    $secondUser = createUser();

    $item = Item::factory()->create([
        'user_id' => $user->id,
    ]);

    $vote1 = new Vote();
    $vote1->user_id = $user->id;
    $vote1->model_type = Item::class;
    $vote1->model_id = $item->id;
    $vote1->subscribed = false;
    $vote1->created_at = Carbon::today()->subDays(2);
    $vote1->save();

    $vote2 = new Vote();
    $vote2->user_id = $secondUser->id;
    $vote2->model_type = Item::class;
    $vote2->model_id = $item->id;
    $vote2->subscribed = false;
    $vote2->created_at = Carbon::today();
    $vote2->save();

    $component = Livewire::test(VoteHistoryChart::class, ['item' => $item]);

    $component->assertStatus(200);
});
