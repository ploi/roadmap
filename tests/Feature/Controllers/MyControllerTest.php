<?php

use App\Livewire\My;
use App\Models\User;
use function Pest\Laravel\get;

use App\Livewire\RecentMentions;
use function Pest\Laravel\actingAs;

test('it renders view', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertOk();
});

test('it shows breadcrumbs', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertSee(trans('items.my-items'));
});

test('view has live components', function ($component) {
    $user = User::factory()->create();

    actingAs($user)->get(route('my'))->assertSeeLivewire($component);
})->with([
    'My' => My::class,
    'RecentMentions' => RecentMentions::class,
]);

test('user must be logged in', function () {
    get(route('my'))->assertRedirect('login');
});
