<?php

use App\Models\Item;
use Livewire\Livewire;
use App\Models\Comment;
use App\Models\Project;
use App\Livewire\Welcome\RecentComments;

test('recent comments exclude comments from private projects', function () {
    $privateProject = Project::factory()->private()->create();
    $privateItem = Item::factory()->create(['project_id' => $privateProject->id]);
    $publicItem = Item::factory()->create();

    Comment::factory()->create([
        'item_id' => $privateItem->id,
        'content' => 'Private project comment',
    ]);
    Comment::factory()->create([
        'item_id' => $publicItem->id,
        'content' => 'Public item comment',
    ]);

    Livewire::test(RecentComments::class)
        ->assertSee('Public item comment')
        ->assertDontSee('Private project comment');
});
