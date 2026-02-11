<?php

use App\Models\Item;
use App\Models\Board;
use Livewire\Livewire;
use App\Models\Comment;
use App\Models\Project;
use App\Livewire\Project\BoardColumn;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('renders successfully', function () {
    $project = Project::factory()->has(Board::factory())->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->assertStatus(200);
});

it('displays items', function () {
    $project = Project::factory()
        ->has(Board::factory()->has(Item::factory(3)))
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->assertSee($board->items->pluck('title')->toArray());
});

it('defaults to created_at sort', function () {
    $project = Project::factory()
        ->has(Board::factory()->state(['sort_items_by' => 'popular']))
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->assertSet('sortBy', 'created_at');
});

it('sorts by newest when setSortBy is called with created_at', function () {
    $project = Project::factory()
        ->has(
            Board::factory()->has(
                Item::factory(2)->state(new Sequence(
                    ['title' => 'older item', 'created_at' => now()->subDay()],
                    ['title' => 'newer item', 'created_at' => now()],
                ))
            )
        )
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->call('setSortBy', 'created_at')
        ->assertSeeInOrder(['newer item', 'older item']);
});

it('sorts by most voted when setSortBy is called with total_votes', function () {
    $project = Project::factory()
        ->has(
            Board::factory()->has(
                Item::factory(2)->state(new Sequence(
                    ['title' => 'low votes', 'total_votes' => 1],
                    ['title' => 'high votes', 'total_votes' => 10],
                ))
            )
        )
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->call('setSortBy', 'total_votes')
        ->assertSeeInOrder(['high votes', 'low votes']);
});

it('sorts by last commented', function () {
    $project = Project::factory()
        ->has(Board::factory()->has(Item::factory(2)->state(new Sequence(
            ['title' => 'old comment item'],
            ['title' => 'new comment item'],
        ))))
        ->create();
    $board = $project->boards->first();
    $items = $board->items;

    Comment::factory()->create([
        'item_id' => $items[0]->id,
        'created_at' => now()->subDay(),
    ]);
    Comment::factory()->create([
        'item_id' => $items[1]->id,
        'created_at' => now(),
    ]);

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->call('setSortBy', 'last_commented')
        ->assertSeeInOrder(['new comment item', 'old comment item']);
});

it('keeps pinned items at the top regardless of sort', function () {
    $project = Project::factory()
        ->has(
            Board::factory()->has(
                Item::factory(2)->state(new Sequence(
                    ['title' => 'unpinned high votes', 'pinned' => false, 'total_votes' => 100],
                    ['title' => 'pinned low votes', 'pinned' => true, 'total_votes' => 1],
                ))
            )
        )
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->call('setSortBy', 'total_votes')
        ->assertSeeInOrder(['pinned low votes', 'unpinned high votes']);
});

it('shows empty state when board has no items', function () {
    $project = Project::factory()->has(Board::factory())->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->assertSee(trans('items.no-items'));
});

it('filters items by search query', function () {
    $project = Project::factory()
        ->has(
            Board::factory()->has(
                Item::factory(2)->state(new Sequence(
                    ['title' => 'Add dark mode support'],
                    ['title' => 'Fix login bug'],
                ))
            )
        )
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->set('search', 'dark mode')
        ->assertSeeText('Add dark mode support')
        ->assertDontSeeText('Fix login bug');
});

it('shows all items when search is cleared', function () {
    $project = Project::factory()
        ->has(
            Board::factory()->has(
                Item::factory(2)->state(new Sequence(
                    ['title' => 'Add dark mode support'],
                    ['title' => 'Fix login bug'],
                ))
            )
        )
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->set('search', 'dark mode')
        ->assertDontSeeText('Fix login bug')
        ->set('search', '')
        ->assertSeeText('Add dark mode support')
        ->assertSeeText('Fix login bug');
});

it('ignores invalid sort values', function () {
    $project = Project::factory()
        ->has(Board::factory())
        ->create();
    $board = $project->boards->first();

    Livewire::test(BoardColumn::class, ['project' => $project, 'board' => $board])
        ->call('setSortBy', 'invalid_sort')
        ->assertSet('sortBy', 'created_at');
});
