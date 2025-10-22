<?php

use App\Models\Item;
use App\Models\Vote;
use App\Models\Board;
use Livewire\Livewire;
use App\Models\Project;
use App\Livewire\Item\Create;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\ItemHasBeenCreatedEmail;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->project = Project::factory()->create();
    $this->board = Board::factory()->create(['project_id' => $this->project->getAttributeValue('id')]);

    app(GeneralSettings::class)->send_notifications_to = [['webhook' => 'info@ploi.io', 'type' => 'email']];
});

test('A user can submit a new item via the board page', function () {
    $user = createAndLoginUser();

    $livewire = Livewire::test(Create::class, ['project' => $this->project, 'board' => $this->board])
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit');

    $item = $user->items()->first();

    $livewire->assertRedirect(route('projects.items.show', [$item->project, $item]));

    assertDatabaseHas(Item::class, [
        'title' => 'An example title',
        'content' => 'The content of an item.',
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'total_votes' => 1,
    ]);

    assertDatabaseHas(Vote::class, [
        'model_id' => $item->id,
        'user_id' => $item->user->id,
    ]);

    Mail::assertQueued(ItemHasBeenCreatedEmail::class);
});

test('validation rules are enforced when creating an item', function () {
    Livewire::test(Create::class, ['project' => $this->project, 'board' => $this->board])
        ->set('title', '')
        ->set('content', '')
        ->call('submit')
        ->assertHasErrors(['title' => 'required', 'content' => 'required']);
});

test('A user can not submit a new item via the board page if disabled', function () {
    createAndLoginUser();

    $this->board->updateQuietly(['can_users_create' => false]);

    Livewire::test(Create::class, ['project' => $this->project, 'board' => $this->board])
        ->set('title', 'An example title')
        ->set('content', 'The content of an item.')
        ->call('submit')
        ->assertRedirect(route('projects.boards.show', [$this->project, $this->board]));

    assertDatabaseCount(Item::class, 0);
});

test('Private items do not send notifications', function () {
    $user = createAndLoginUser();

    Item::factory()->private()->create([
        'title' => 'A private item',
        'content' => 'This is private content.',
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $user->id,
    ]);

    assertDatabaseHas(Item::class, [
        'title' => 'A private item',
        'content' => 'This is private content.',
        'private' => true,
    ]);

    Mail::assertNothingQueued();
});

test('Public items send notifications', function () {
    $user = createAndLoginUser();

    Item::factory()->create([
        'title' => 'A public item',
        'content' => 'This is public content.',
        'project_id' => $this->project->id,
        'board_id' => $this->board->id,
        'user_id' => $user->id,
        'private' => false,
    ]);

    assertDatabaseHas(Item::class, [
        'title' => 'A public item',
        'content' => 'This is public content.',
        'private' => false,
    ]);

    Mail::assertQueued(ItemHasBeenCreatedEmail::class);
});
