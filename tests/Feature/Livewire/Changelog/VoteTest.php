<?php

use App\Models\Vote;
use Livewire\Livewire;
use App\Models\Changelog;
use App\Livewire\Changelog\Vote as VoteComponent;

test('liking a changelog adds a vote for the user', function () {
    $user = createAndLoginUser();
    $changelog = Changelog::factory()->published()->for(createUser())->create();

    Livewire::test(VoteComponent::class, ['changelog' => $changelog])
        ->call('vote')
        ->assertSeeHtml('aria-pressed="true"');

    expect($changelog->hasVoted($user))->toBeTrue();
});

test('liking a changelog again removes the vote', function () {
    $user = createAndLoginUser();
    $changelog = Changelog::factory()->published()->for(createUser())->create();
    $changelog->toggleUpvote($user);

    Livewire::test(VoteComponent::class, ['changelog' => $changelog])
        ->call('vote')
        ->assertSeeHtml('aria-pressed="false"');

    expect($changelog->hasVoted($user))->toBeFalse();
});

test('guests are sent to the login page when liking a changelog', function () {
    $changelog = Changelog::factory()->published()->for(createUser())->create();

    Livewire::test(VoteComponent::class, ['changelog' => $changelog])
        ->call('vote')
        ->assertRedirect(route('login'));

    expect(Vote::count())->toBe(0);
});

test('changelog likes show the voters only when requested', function (bool $showVoters) {
    $voter = createUser();
    $changelog = Changelog::factory()->published()->for(createUser())->create();
    $changelog->toggleUpvote($voter);

    Livewire::test(VoteComponent::class, ['changelog' => $changelog, 'showVoters' => $showVoters])
        ->{$showVoters ? 'assertSee' : 'assertDontSee'}(route('public-user', $voter->username), false);
})->with([
    'with voters' => [true],
    'without voters' => [false],
]);
