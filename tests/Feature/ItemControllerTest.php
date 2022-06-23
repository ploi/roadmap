<?php

use function Pest\Laravel\get;
use App\Models\{Board, Item, Project};

it('renders the items page without a project', function () {

    $item = Item::factory()->create();

    get(route('items.show',$item))->assertOk();

});

it('renders the items page with a project', function () {

    $project = Project::factory()->create();

    $board = Board::factory()
        ->for($project)
        ->create();

    $item = Item::factory()
        ->for($project)
        ->for($board)
        ->create();

    get(route('projects.items.show',[$project,$item]))->assertOk();

});
