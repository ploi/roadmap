<?php

use Livewire\Livewire;
use App\Livewire\SpotlightSearch;
use Livewire\Mechanisms\HandleComponents\Checksum;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

it('rejects an update for a snapshot signed without the path and method of its page with a 404', function () {
    $snapshot = Livewire::test(SpotlightSearch::class)->snapshot;
    unset($snapshot['memo']['path'], $snapshot['memo']['method'], $snapshot['checksum']);
    $snapshot['checksum'] = Checksum::generate($snapshot);
    $this->withoutDeprecationHandling();

    $response = $this->withHeader('X-Livewire', '1')->postJson(app(HandleRequests::class)->getUpdateUri(), [
        'components' => [['snapshot' => json_encode($snapshot), 'updates' => [], 'calls' => []]],
    ]);

    $response->assertNotFound();
});

it('answers an update without components with a 404 that renders no components', function () {
    $response = $this->json('POST', app(HandleRequests::class)->getUpdateUri(), ['components' => []], ['X-Livewire' => '1', 'Accept' => '*/*']);

    $response->assertNotFound()->assertDontSee('wire:snapshot', false);
});
