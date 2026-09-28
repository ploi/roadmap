<?php

use App\Models\User;
use App\Enums\UserRole;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withToken;

function listToolsRequest(): array
{
    return ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list', 'params' => []];
}

it('requires an api token', function () {
    postJson('/mcp', listToolsRequest())->assertUnauthorized();
});

it('rejects an invalid api token', function () {
    withToken('invalid-token')->postJson('/mcp', listToolsRequest())->assertUnauthorized();
});

it('lists the tools for a regular user without the move tool', function () {
    $token = User::factory()->create(['role' => UserRole::User])->createToken('MCP')->plainTextToken;

    $tools = withToken($token)->postJson('/mcp', listToolsRequest())
        ->assertOk()
        ->json('result.tools.*.name');

    expect($tools)
        ->toContain('list-projects', 'get-project', 'list-items', 'get-item', 'comment-on-item')
        ->not->toContain('move-item');
});

it('lists the move tool for employees', function () {
    $token = User::factory()->create(['role' => UserRole::Employee])->createToken('MCP')->plainTextToken;

    $tools = withToken($token)->postJson('/mcp', listToolsRequest())
        ->assertOk()
        ->json('result.tools.*.name');

    expect($tools)->toContain('move-item');
});
