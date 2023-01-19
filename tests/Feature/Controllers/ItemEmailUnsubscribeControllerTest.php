<?php

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

it('can unsubscribe a user from an item', function () {

    $user = User::factory()->create();
    $item = Item::factory()->create();

    DB::table('votes')->insert([
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => true,
    ]);

    $this->assertTrue($user->isSubscribedToItem($item));

    $response = $this->get(URL::signedRoute('items.email-unsubscribe', [
        'item' => $item,
        'user' => $user,
    ]));

    $this->assertFalse($user->isSubscribedToItem($item));

    $this->assertDatabaseHas('votes', [
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => false,
    ]);

    $response->assertRedirect(route('items.show', $item->getAttributeValue('slug')));
    $this->assertGuest();
});

it('does not resubscribe a user who clicks the link whilst unsubscribed', function () {

    $user = User::factory()->create();
    $item = Item::factory()->create();

    DB::table('votes')->insert([
        'user_id' => $user->id,
        'model_id' => $item->id,
        'model_type' => Item::class,
        'subscribed' => false,
    ]);

    $this->assertFalse($user->isSubscribedToItem($item));

    $response = $this->get(URL::signedRoute('items.email-unsubscribe', [
        'item' => $item,
        'user' => $user,
    ]));

    $this->assertFalse($user->isSubscribedToItem($item));
    $response->assertRedirect(route('home'));
    $this->assertGuest();
});

it('returns a 403 if the signed link has been modified', function () {

    $user = User::factory()->create();
    $item = Item::factory()->create();

    $response = $this->get(URL::signedRoute('items.email-unsubscribe', [
        'item' => $item,
        'user' => $user,
    ]) . '&foo=bar');

    $response->assertStatus(403);
});

it('returns a 404 if the item does not exist', function () {

    $user = User::factory()->create();
    $item = Item::factory()->create();

    $response = $this->get(URL::signedRoute('items.email-unsubscribe', [
        'item' => 999,
        'user' => $user,
    ]));

    $response->assertStatus(404);
});

it('returns a 404 if the user does not exist', function () {

    $user = User::factory()->create();
    $item = Item::factory()->create();

    $response = $this->get(URL::signedRoute('items.email-unsubscribe', [
        'item' => $item,
        'user' => 999,
    ]));

    $response->assertStatus(404);
});
