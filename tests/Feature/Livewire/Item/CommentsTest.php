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

test('a normal user cannot create a private note through component state', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user);

    Livewire::test(Comments::class, ['item' => $item])
        ->set('content', 'Valid public content')
        ->set('private_content', 'Forged private note')
        ->call('submit')
        ->assertForbidden();

    expect($item->comments()->where('private', true)->exists())->toBeFalse();
});

test('an administrator can create a private note', function () {
    $admin = User::factory()->admin()->create();
    $item = Item::factory()->create();

    $this->actingAs($admin);

    Livewire::test(Comments::class, ['item' => $item])
        ->set('private_content', 'Authorized private note')
        ->call('submit');

    expect($item->comments()
        ->where('content', 'Authorized private note')
        ->where('private', true)
        ->exists())->toBeTrue();
});
