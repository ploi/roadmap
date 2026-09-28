<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Profile;
use App\Settings\GeneralSettings;
use Laravel\Sanctum\PersonalAccessToken;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(fn () => GeneralSettings::fake(['enable_mcp' => true]));

it('creates an mcp token and shows it once', function () {
    $user = createAndLoginUser();

    $component = Livewire::test(Profile::class)
        ->callAction('createMcpToken', ['name' => 'Claude'])
        ->assertHasNoActionErrors();

    expect($component->get('newMcpToken'))->not->toBeNull();

    $component->assertSee($component->get('newMcpToken'));

    assertDatabaseHas(PersonalAccessToken::class, [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
        'name' => 'Claude',
    ]);
});

it('revokes an mcp token', function () {
    $user = createAndLoginUser();
    $token = $user->createToken('Claude')->accessToken;

    Livewire::test(Profile::class)
        ->assertSee('Claude')
        ->callAction('revokeMcpToken', arguments: ['token' => $token->id]);

    assertDatabaseMissing(PersonalAccessToken::class, ['id' => $token->id]);
});

it('does not revoke tokens of other users', function () {
    createAndLoginUser();
    $token = User::factory()->create()->createToken('Someone else')->accessToken;

    Livewire::test(Profile::class)
        ->callAction('revokeMcpToken', arguments: ['token' => $token->id]);

    assertDatabaseHas(PersonalAccessToken::class, ['id' => $token->id]);
});

it('hides mcp access when an admin has disabled mcp', function () {
    GeneralSettings::fake(['enable_mcp' => false]);

    createAndLoginUser();

    Livewire::test(Profile::class)
        ->assertDontSee(trans('profile.mcp.heading'))
        ->assertActionHidden('createMcpToken');
});
