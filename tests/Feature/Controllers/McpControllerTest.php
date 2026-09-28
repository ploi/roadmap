<?php

use App\Enums\UserRole;
use function Pest\Laravel\get;
use App\Settings\GeneralSettings;

it('explains how to connect to the mcp server', function () {
    GeneralSettings::fake(['enable_mcp' => true]);

    get(route('mcp.docs'))
        ->assertOk()
        ->assertSee(url('mcp'))
        ->assertSee(['list-projects', 'get-item', 'comment-on-item', 'move-item'])
        ->assertSeeText(['Claude Code', 'Claude Desktop', 'ChatGPT', 'Cursor', 'VS Code', 'Codex'])
        ->assertSeeText(trans('mcp.step-token-login'))
        ->assertDontSeeText(trans('mcp.disabled'));
});

it('links logged in users to their profile to create a token', function () {
    GeneralSettings::fake(['enable_mcp' => true]);

    createAndLoginUser();

    get(route('mcp.docs'))
        ->assertOk()
        ->assertSee(route('profile'))
        ->assertSeeText(trans('mcp.step-token-button'));
});

it('is not available when mcp is disabled', function (?UserRole $role) {
    GeneralSettings::fake(['enable_mcp' => false]);

    if ($role) {
        createAndLoginUser(['role' => $role]);
    }

    get(route('mcp.docs'))->assertNotFound();
})->with([null, UserRole::User]);

it('is available to admins and employees when mcp is disabled', function (UserRole $role) {
    GeneralSettings::fake(['enable_mcp' => false]);

    createAndLoginUser(['role' => $role]);

    get(route('mcp.docs'))
        ->assertOk()
        ->assertSeeText(trans('mcp.disabled'));
})->with([UserRole::Admin, UserRole::Employee]);
