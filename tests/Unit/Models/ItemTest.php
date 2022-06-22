<?php

use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use App\Models\User;
use App\Models\Vote;

test('popular scope returns highest voted items', function () {

    $items = Item::factory(10)->create();

    $items->each(function ($item) {
        $item->votes()->saveMany(Vote::factory(3)->create(['model_type' => 'App\Models\Item', 'model_id' => $item->id]));
        $item->update(['total_votes' => $item->votes()->count()]);
    });

    Item::query()->popular()->get()->each(function ($item) {
        expect($item->total_votes)->toBeGreaterThan(0);
    });
});

test('visible scope returns items the user can see', function () {

    $adminOnlyItem = Item::factory()->create(['private' => true]);
    $publicItem = Item::factory()->create(['private' => false]);

    $items = Item::query()->visibleForCurrentUser()->get();

    expect($items->contains($adminOnlyItem))->toBe(false);

    $this->assertGuest();
});

test('admins can see private items on visible scope', function () {

    $adminOnlyItem = Item::factory()->create(['private' => true]);
    $publicItem = Item::factory()->create(['private' => false]);

    $user = createUser(['role' => User::ROLE_ADMIN]);

    $this->actingAs($user);

    $items = Item::query()->visibleForCurrentUser()->get();

    expect($items->contains($adminOnlyItem))->toBe(true)
        ->and($items->contains($publicItem))->toBe(true);

    $this->assertAuthenticatedAs($user);
});

test('employees can see private items on visible scope', function () {

    $adminOnlyItem = Item::factory()->create(['private' => true]);
    $publicItem = Item::factory()->create(['private' => false]);

    $user = createUser(['role' => User::ROLE_EMPLOYEE]);

    $this->actingAs($user);

    $items = Item::query()->visibleForCurrentUser()->get();

    expect($items->contains($adminOnlyItem))->toBe(true)
        ->and($items->contains($publicItem))->toBe(true);

    $this->assertAuthenticatedAs($user);
});

test('scope returns items that have no project and board', function () {

    $itemNoAssociations = Item::factory()->create();
    $itemWithAssociations = Item::factory()->create([
        'project_id' => Project::factory()->create()->getKey(),
        'board_id' => Board::factory()->create()->getKey(),
    ]);

    $items = Item::query()->HasNoProjectAndBoard()->get();

    expect($items->contains($itemNoAssociations))->toBe(true)
        ->and($items->contains($itemWithAssociations))->toBe(false);
});

test('returns true if the user has voted on an item', function () {

    $user = createUser();
    $item = Item::factory()->create();

    $item->votes()->saveMany(Vote::factory(3)->create(['model_type' => 'App\Models\Item', 'model_id' => $item->id, 'user_id' => $user->getKey()]));

    expect($item->hasVoted($user))->toBeTruthy();
    $this->assertEquals(3, $user->votes()->count());
});


test('returns false if the user has not voted on an item', function () {

    $user = createUser();
    $item = Item::factory()->create();

    expect($item->hasVoted($user))->toBeFalsy();
    $this->assertEquals(0, $user->votes()->count());
});

test('returns the vote details for a user', function () {

    $user = createUser();
    $item = Item::factory()->create();

    $item->votes()->saveMany(Vote::factory(3)->create(['model_type' => 'App\Models\Item', 'model_id' => $item->id, 'user_id' => $user->getKey()]));

    expect($item->getUserVote($user))->toBeInstanceOf(Vote::class);
});

test('increments the vote for a user', function () {

    $user = createUser();
    $item = Item::factory()->create();

    $item->toggleUpvote($user);
    $this->assertEquals(1, $user->votes()->count());
});

test('deincriments the vote for a user', function () {

    $user = createUser();
    $item = Item::factory()->create();

    $item->votes()->saveMany(Vote::factory(1)->create(['model_type' => 'App\Models\Item', 'model_id' => $item->id, 'user_id' => $user->getKey()]));

    $item->toggleUpvote($user);
    $this->assertEquals(0, $user->votes()->count());
});

test('returns true if item is pinned', function () {

    $item = Item::factory()->create(['pinned' => true]);

    expect($item->isPinned())->toBeTruthy();
});

test('returns false if item is not pinned', function () {

    $item = Item::factory()->create(['pinned' => false]);

    expect($item->isPinned())->toBeFalsy();
});

test('returns true if item is private', function () {

    $item = Item::factory()->create(['private' => true]);

    expect($item->isPrivate())->toBeTruthy();
});

test('returns false if item is public', function () {

    $item = Item::factory()->create(['private' => false]);

    expect($item->isPrivate())->toBeFalsy();
});
