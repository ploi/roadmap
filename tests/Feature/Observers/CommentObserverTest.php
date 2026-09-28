<?php

use App\Models\Item;
use App\Models\Comment;
use App\Notifications\MentionNotification;
use Illuminate\Support\Facades\Notification;

test('a mention in a new comment is linked and notifies the mentioned user', function () {
    Notification::fake();

    $author = createUser(['name' => 'Alex Bouma']);
    $kenneth = createUser(['name' => 'Kenneth Wolfs']);

    $comment = Comment::factory()->for(Item::factory())->for($author)->create([
        'content' => 'Hey @Kenneth Wolfs',
    ]);

    expect($comment->refresh()->content)->toBe('Hey [@Kenneth Wolfs](/user/kenneth-wolfs)');

    Notification::assertSentToTimes($kenneth, MentionNotification::class, 1);
});

test('editing a comment only notifies users that are newly mentioned', function () {
    Notification::fake();

    $author = createUser(['name' => 'Alex Bouma']);
    $kenneth = createUser(['name' => 'Kenneth Wolfs']);
    $dennis = createUser(['name' => 'Dennis Smink']);

    $comment = Comment::factory()->for(Item::factory())->for($author)->create([
        'content' => 'Hey @Kenneth Wolfs',
    ]);

    $comment->refresh()->update(['content' => $comment->content . ' and @Dennis Smink']);

    expect($comment->refresh()->content)
        ->toBe('Hey [@Kenneth Wolfs](/user/kenneth-wolfs) and [@Dennis Smink](/user/dennis-smink)');

    Notification::assertSentToTimes($kenneth, MentionNotification::class, 1);
    Notification::assertSentToTimes($dennis, MentionNotification::class, 1);
});

test('mentioning yourself does not notify you', function () {
    Notification::fake();

    $author = createUser(['name' => 'Alex Bouma']);

    Comment::factory()->for(Item::factory())->for($author)->create([
        'content' => 'Note to @Alex Bouma',
    ]);

    Notification::assertNotSentTo($author, MentionNotification::class);
});
