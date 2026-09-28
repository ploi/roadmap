<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use Livewire\Livewire;
use App\Models\Comment;
use App\Models\Project;
use App\Settings\GeneralSettings;
use Filament\Actions\Testing\TestAction;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use App\Livewire\Item\Comment as CommentComponent;

beforeEach(function () {
    GeneralSettings::fake([
        'users_must_verify_email' => false,
        'profanity_words' => ['badword'],
    ]);

    $this->author = createUser();
    $this->project = Project::factory()->create();
    $this->board = Board::factory()->create(['project_id' => $this->project->id]);
    $this->item = Item::factory()->create([
        'user_id' => $this->author->id,
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
    ]);
    $this->comment = $this->item->comments()->create([
        'user_id' => $this->author->id,
        'content' => 'A public comment',
    ]);
});

function commentComponent(Item $item, Comment $comment)
{
    return Livewire::test(CommentComponent::class, [
        'comments' => collect(),
        'comment' => $comment,
        'item' => $item,
        'reply' => null,
    ]);
}

function replyTo(Comment $comment): TestAction
{
    return TestAction::make('reply')->arguments(['comment' => $comment->id]);
}

function editComment(Comment $comment): TestAction
{
    return TestAction::make('edit')->arguments(['comment' => $comment->id]);
}

test('a user can reply to a comment on a visible item', function () {
    $user = createAndLoginUser();

    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($this->comment), data: ['content' => 'A reply'])
        ->assertHasNoFormErrors()
        ->assertRedirect(route('items.show', $this->item->slug));

    assertDatabaseHas(Comment::class, [
        'item_id' => $this->item->id,
        'parent_id' => $this->comment->id,
        'user_id' => $user->id,
        'content' => 'A reply',
    ]);
});

test('a user can not reply to a comment on an item they can not see', function (Closure $makeHiddenItem) {
    createAndLoginUser();

    $hiddenItem = $makeHiddenItem($this->author);
    $hiddenComment = $hiddenItem->comments()->create([
        'user_id' => $this->author->id,
        'content' => 'A hidden comment',
    ]);

    expect(fn () => commentComponent($this->item, $this->comment)
        ->callAction(replyTo($hiddenComment), data: ['content' => 'A sneaky reply']))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    assertDatabaseMissing(Comment::class, ['content' => 'A sneaky reply']);
})->with([
    'private item' => fn (User $author) => Item::factory()->private()->create(['user_id' => $author->id]),
    'item in private project' => fn (User $author) => Item::factory()->create([
        'user_id' => $author->id,
        'project_id' => Project::factory()->private()->create()->id,
    ]),
]);

test('an admin can reply to a comment on a private item', function () {
    $admin = createAndLoginUser(user: User::factory()->admin()->create());

    $privateItem = Item::factory()->private()->create(['user_id' => $this->author->id]);
    $privateItemComment = $privateItem->comments()->create([
        'user_id' => $this->author->id,
        'content' => 'A comment on a private item',
    ]);

    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($privateItemComment), data: ['content' => 'An admin reply'])
        ->assertHasNoFormErrors();

    assertDatabaseHas(Comment::class, [
        'parent_id' => $privateItemComment->id,
        'user_id' => $admin->id,
        'content' => 'An admin reply',
    ]);
});

test('a user can not reply to a private note', function () {
    createAndLoginUser();

    $privateNote = $this->item->comments()->create([
        'user_id' => User::factory()->admin()->create()->id,
        'content' => 'An internal note',
        'private' => true,
    ]);

    expect(fn () => commentComponent($this->item, $this->comment)
        ->callAction(replyTo($privateNote), data: ['content' => 'A sneaky reply']))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    assertDatabaseMissing(Comment::class, ['content' => 'A sneaky reply']);
});

test('a user can not reply when the board blocks comments', function () {
    createAndLoginUser();

    $this->board->update(['block_comments' => true]);

    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($this->comment), data: ['content' => 'A blocked reply'])
        ->assertForbidden();

    assertDatabaseMissing(Comment::class, ['content' => 'A blocked reply']);
});

test('a guest is redirected to login when replying', function () {
    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($this->comment), data: ['content' => 'A guest reply'])
        ->assertRedirect(route('login'));

    assertDatabaseMissing(Comment::class, ['content' => 'A guest reply']);
});

test('an unverified user is redirected to verify their email when replying', function () {
    app(GeneralSettings::class)->users_must_verify_email = true;
    createAndLoginUser(user: User::factory()->unverified()->create());

    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($this->comment), data: ['content' => 'An unverified reply'])
        ->assertRedirect(route('verification.notice'));

    assertDatabaseMissing(Comment::class, ['content' => 'An unverified reply']);
});

test('a reply containing profanity is rejected', function () {
    createAndLoginUser();

    commentComponent($this->item, $this->comment)
        ->callAction(replyTo($this->comment), data: ['content' => 'A badword reply'])
        ->assertHasFormErrors(['content']);

    assertDatabaseMissing(Comment::class, ['content' => 'A badword reply']);
});

test('a user can edit their own comment', function () {
    $user = createAndLoginUser();

    $ownComment = $this->item->comments()->create([
        'user_id' => $user->id,
        'content' => 'Original content',
    ]);

    commentComponent($this->item, $ownComment)
        ->mountAction(editComment($ownComment))
        ->assertSchemaStateSet(['content' => 'Original content'])
        ->fillForm(['content' => 'Updated content'])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect($ownComment->fresh()->content)->toBe('Updated content');
});

test('the edit form does not expose the content of another user\'s comment', function () {
    createAndLoginUser();

    $privateNote = $this->item->comments()->create([
        'user_id' => User::factory()->admin()->create()->id,
        'content' => 'Secret internal note',
        'private' => true,
    ]);

    expect(fn () => commentComponent($this->item, $this->comment)
        ->mountAction(editComment($privateNote)))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
