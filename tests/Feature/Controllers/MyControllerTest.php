<?php

use App\Http\Livewire\My;
use App\Http\Livewire\RecentMentions;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('it renders view', function () {

    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertOk();
});

test('it shows breadcrumbs', function () {

    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertSee('My items');
});

test('view has live compoents', function ($component) {

    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertSeeLivewire($component);
})->with([
    'My' => My::class,
    'RecentMentions' => RecentMentions::class,
]);

test('user must be logged in', function () {
    get(route('my'))->assertRedirect('login');
});
