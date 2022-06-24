<?php

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

test('private projects are not visible in navbar for users', function(?UserRole $userRole, bool $shouldBeVisible) {
    $project = Project::factory()->private()->create();

    createAndLoginUser(['role' => $userRole]);

    get('/')->{$shouldBeVisible ? 'assertSeeText' : 'assertDontSeeText'}($project->title);
})->with([
    [UserRole::User, false],
    [UserRole::Employee, true],
    [UserRole::Admin, true],
]);
