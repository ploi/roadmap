<?php

use App\Models\Item;
use App\Models\Board;
use App\Models\Project;
use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use App\Filament\Resources\Items\Pages\EditItem;

beforeEach(function () {
    $this->admin = createUser(['role' => UserRole::Admin]);
    $this->project = Project::factory()->create();
    $this->board = Board::factory()->create(['project_id' => $this->project->id]);
});

function createVoteForItem(Item $item, User $user): void
{
    $vote = $item->votes()->create();
    $vote->user()->associate($user)->save();
}

test('merging an item transfers votes to the target item', function () {
    $sourceItem = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->admin->id,
    ]);

    $targetItem = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->admin->id,
    ]);

    $voter1 = createUser();
    $voter2 = createUser();

    createVoteForItem($sourceItem, $voter1);
    createVoteForItem($sourceItem, $voter2);

    expect($sourceItem->votes()->count())->toBe(2);
    expect($targetItem->votes()->count())->toBe(0);

    $this->actingAs($this->admin);

    Livewire::test(EditItem::class, ['record' => $sourceItem->slug])
        ->callAction('merge item', [
            'item_id' => $targetItem->id,
            'private' => true,
        ])
        ->assertHasNoActionErrors();

    expect($targetItem->votes()->count())->toBe(2);
    expect($targetItem->fresh()->total_votes)->toBe(2);
});

test('merging an item does not duplicate votes for users who already voted on target', function () {
    $sourceItem = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->admin->id,
    ]);

    $targetItem = Item::factory()->create([
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $this->admin->id,
    ]);

    $voter1 = createUser();
    $voter2 = createUser();
    $voter3 = createUser();

    // voter1 voted on both items
    createVoteForItem($sourceItem, $voter1);
    createVoteForItem($sourceItem, $voter2);

    createVoteForItem($targetItem, $voter1);
    createVoteForItem($targetItem, $voter3);

    expect($sourceItem->votes()->count())->toBe(2);
    expect($targetItem->votes()->count())->toBe(2);

    $this->actingAs($this->admin);

    Livewire::test(EditItem::class, ['record' => $sourceItem->slug])
        ->callAction('merge item', [
            'item_id' => $targetItem->id,
            'private' => true,
        ])
        ->assertHasNoActionErrors();

    // voter1 already voted on target, so only voter2 is transferred (2 existing + 1 new = 3)
    expect($targetItem->votes()->count())->toBe(3);
    expect($targetItem->fresh()->total_votes)->toBe(3);

    // Ensure voter1 still only has one vote on the target
    expect($targetItem->votes()->where('user_id', $voter1->id)->count())->toBe(1);
});
