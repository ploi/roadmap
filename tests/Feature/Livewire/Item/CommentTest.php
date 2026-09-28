<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use Livewire\Livewire;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Testing\TestAction;
use App\Livewire\Item\Comment as CommentComponent;

function mountComment(Comment $comment): \Livewire\Features\SupportTesting\Testable
{
    return Livewire::test(CommentComponent::class, [
        'comments' => collect(),
        'comment' => $comment,
        'item' => $comment->item,
    ]);
}

test('an admin can delete a comment and its replies', function () {
    createAndLoginUser(user: User::factory()->admin()->create());

    $item = Item::factory()->create();
    $comment = Comment::factory()->for($item)->for(User::factory())->create();
    $reply = Comment::factory()->for($item)->for(User::factory())->create(['parent_id' => $comment->id]);

    mountComment($comment)
        ->callAction(TestAction::make('delete')->arguments(['comment' => $comment->id]))
        ->assertRedirect(route('items.show', $item->slug));

    $this->assertModelMissing($comment);
    $this->assertModelMissing($reply);
});

test('a non admin cannot delete a comment', function () {
    $user = createAndLoginUser();

    $comment = Comment::factory()->for(Item::factory())->for($user)->create();

    mountComment($comment)
        ->assertActionHidden(TestAction::make('delete')->arguments(['comment' => $comment->id]))
        ->call('mountAction', 'delete', ['comment' => $comment->id])
        ->call('callMountedAction');

    $this->assertModelExists($comment);
});

test('a user can reply inline to a comment', function () {
    $user = createAndLoginUser();

    $item = Item::factory()->create();
    $comment = Comment::factory()->for($item)->for(User::factory())->create();

    $component = mountComment($comment)
        ->callAction(TestAction::make('reply'))
        ->assertSet('isReplying', true)
        ->set('replyContent', 'Thanks for the idea!')
        ->call('submitReply');

    $reply = Comment::query()->where('parent_id', $comment->id)->sole();

    expect($reply->content)->toBe('Thanks for the idea!')
        ->and($reply->user_id)->toBe($user->id);

    $component->assertRedirect($item->view_url . '#comment-' . $reply->id);
});

test('an inline reply needs content', function () {
    createAndLoginUser();

    $comment = Comment::factory()->for(Item::factory())->for(User::factory())->create();

    mountComment($comment)
        ->callAction(TestAction::make('reply'))
        ->set('replyContent', '')
        ->call('submitReply')
        ->assertHasErrors(['replyContent' => 'required']);

    expect(Comment::query()->where('parent_id', $comment->id)->exists())->toBeFalse();
});

test('replying is refused when the board blocks comments', function () {
    createAndLoginUser();

    $board = Board::factory()->for(Project::factory())->create(['block_comments' => true]);
    $item = Item::factory()->for($board)->create();
    $comment = Comment::factory()->for($item)->for(User::factory())->create();

    mountComment($comment)
        ->set('replyContent', 'Sneaky reply')
        ->call('submitReply')
        ->assertForbidden();

    expect(Comment::query()->where('parent_id', $comment->id)->exists())->toBeFalse();
});

test('guests are sent to the login page when replying', function () {
    $comment = Comment::factory()->for(Item::factory())->for(User::factory())->create();

    mountComment($comment)
        ->set('replyContent', 'Anonymous reply')
        ->call('submitReply')
        ->assertRedirect(route('login'));

    expect(Comment::query()->where('parent_id', $comment->id)->exists())->toBeFalse();
});

test('interacting with a comment does not reload the rest of the thread', function () {
    createAndLoginUser();

    $item = Item::factory()->create();
    $comment = Comment::factory()->for($item)->for(User::factory())->create();
    $parent = $comment;

    foreach (range(1, 4) as $depth) {
        $parent = Comment::factory()->for($item)->for(User::factory())->create(['parent_id' => $parent->id]);
    }

    $component = Livewire::test(CommentComponent::class, [
        'comments' => $item->comments()->get()->mapToGroups(fn (Comment $comment) => [(int) $comment->parent_id => $comment]),
        'comment' => $comment,
        'item' => $item,
    ]);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $component->call('cancelReply');

    $commentQueries = collect(DB::getQueryLog())->filter(fn (array $query) => str_contains($query['query'], 'from "comments"'));

    expect($commentQueries)->toHaveCount(1);
});
