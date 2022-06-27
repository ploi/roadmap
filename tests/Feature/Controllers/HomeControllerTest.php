<?php

use App\Models\Item;
use App\Enums\UserRole;
use App\Models\Project;
use function Pest\Laravel\get;

use App\Settings\GeneralSettings;

test('it renders the home page', function () {
    get('/')->assertStatus(200);
});

test('the welcome message is show', function () {
    GeneralSettings::fake(['welcome_text' => 'Welcome to the roadmap']);

    get('/')->assertSee('Welcome to the roadmap');
});

test('the dashboard items are shown', function ($setting, $value) {
    GeneralSettings::fake(['dashboard_items' => $setting]);
    get('/')->assertSee($value);
})->with([
    'recent items' =>['setting' => ["type" => "recent-items", "column_span" => 1, "must_have_board" => false, "must_have_project" => false], 'value' => 'Recent items'],
    'recent comments' => ['setting' => ["type" => "recent-comments", "column_span" => 1, "must_have_board" => false, "must_have_project" => false], 'value' => 'Recent activities'],
]);

test('recent items will not contain private items for users', function (UserRole $userRole, bool $shouldBeVisible) {
    GeneralSettings::fake(['dashboard_items' => [["type" => "recent-items", "column_span" => 1, "must_have_board" => false, "must_have_project" => false]]]);

    Item::factory()->private()->create(['title' => 'Private item']);

    createAndLoginUser(['role' => $userRole]);

    get('/')->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}('Private item');
})->with([
    'User' => [UserRole::User, false],
    'Employee' => [UserRole::Employee, true],
    'Admin' => [UserRole::Admin, true],
]);

test('recent items will not contain items from private projects for users', function (UserRole $userRole, bool $shouldBeVisible) {
    GeneralSettings::fake(['dashboard_items' => [["type" => "recent-items", "column_span" => 1, "must_have_board" => false, "must_have_project" => true]]]);

    $project = Project::factory()->private()->create();
    Item::factory()->for($project)->create(['title' => 'Item in private project']);

    createAndLoginUser(['role' => $userRole]);

    get('/')->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}('Item in private project');
})->with([
    'User' => [UserRole::User, false],
    'Employee' => [UserRole::Employee, true],
    'Admin' => [UserRole::Admin, true],
]);

test('private projects are not visible in navbar for users', function (UserRole $userRole, bool $shouldBeVisible) {
    $project = Project::factory()->private()->create();

    createAndLoginUser(['role' => $userRole]);

    get('/')->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}($project->title);
})->with([
    'User' => [UserRole::User, false],
    'Employee' => [UserRole::Employee, true],
    'Admin' => [UserRole::Admin, true],
]);
