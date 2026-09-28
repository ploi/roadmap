<?php

use App\Models\Item;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Comment;
use Filament\Actions\Testing\TestAction;
use App\Livewire\Item\Comment as CommentComponent;

function mountComment(Comment $comment): \Livewire\Features\SupportTesting\Testable
{
    return Livewire::test(CommentComponent::class, [
        'comments' => collect(),
        'comment' => $comment,
        'item' => $comment->item,
        'reply' => null,
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
