<?php

use App\Models\Item;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Comment;
use App\Livewire\Item\Comments;
use Illuminate\Support\Facades\DB;

function countQueriesRenderingComments(Item $item): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire::test(Comments::class, ['item' => $item]);

    return count(DB::getQueryLog());
}

function addVotedCommentThread(Item $item): void
{
    $comment = Comment::factory()->for($item)->for(User::factory())->create();
    $reply = Comment::factory()->for($item)->for(User::factory())->create(['parent_id' => $comment->id]);
    $privateNote = Comment::factory()->for($item)->for(User::factory())->create(['private' => true]);
    Comment::factory()->for($item)->for(User::factory())->create(['parent_id' => $privateNote->id]);

    foreach ([$comment, $reply] as $votedComment) {
        $votedComment->votes()->create()->user()->associate(User::factory()->create())->save();
    }
}

test('rendering comments does not run extra queries per comment', function () {
    createAndLoginUser(user: User::factory()->admin()->create());

    $item = Item::factory()->create();
    addVotedCommentThread($item);
    $queriesForOneThread = countQueriesRenderingComments($item);

    addVotedCommentThread($item);
    addVotedCommentThread($item);

    expect(countQueriesRenderingComments($item))->toBe($queriesForOneThread);
});
