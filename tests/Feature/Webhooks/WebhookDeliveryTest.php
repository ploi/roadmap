<?php

use App\Models\Item;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Project;
use App\Jobs\SendWebhookJob;
use App\Enums\WebhookEvent;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

function dispatchedEvents(): array
{
    $events = [];

    Bus::assertDispatched(SendWebhookJob::class, function (SendWebhookJob $job) use (&$events) {
        $events[] = $job->event;

        return true;
    });

    return $events;
}

it('queues the events an endpoint subscribed to', function () {
    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create(['events' => ['item.created', 'item.deleted']]);

    $item = Item::factory()->create();
    $item->update(['title' => 'Changed']);
    $item->delete();

    expect(dispatchedEvents())->toBe(['item.created', 'item.deleted']);
});

it('does not queue anything for inactive endpoints', function () {
    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create(['active' => false]);

    Item::factory()->create();

    Bus::assertNotDispatched(SendWebhookJob::class);
});

it('queues update, status change, comment and project events', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->create(['project_id' => $project->id]);
    $item = Item::factory()->create(['project_id' => $project->id]);

    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create();

    $item->update(['board_id' => $board->id]);
    Comment::factory()->create(['item_id' => $item->id]);
    $project->update(['title' => 'Renamed']);

    expect(dispatchedEvents())->toEqualCanonicalizing([
        'item.updated', 'item.status_changed', 'comment.created', 'project.updated',
    ]);

    Bus::assertDispatched(
        SendWebhookJob::class,
        fn (SendWebhookJob $job) => $job->event === 'item.status_changed' && $job->data['board_id'] === $board->id
    );
});

it('sends a signed request and retries with backoff', function () {
    Http::fake(['example.com/*' => Http::response('', 200)]);
    $endpoint = WebhookEndpoint::factory()->create(['secret' => 'top-secret']);

    $job = new SendWebhookJob($endpoint, WebhookEvent::ItemCreated->value, ['id' => 1], 'delivery-1');
    $job->handle();

    Http::assertSent(function ($request) {
        $expected = 'sha256=' . hash_hmac('sha256', $request->header('X-Roadmap-Timestamp')[0] . '.' . $request->body(), 'top-secret');

        return $request->header('X-Roadmap-Signature')[0] === $expected
            && $request->header('X-Roadmap-Event')[0] === 'item.created'
            && $request['data'] === ['id' => 1];
    });

    expect($job->tries)->toBe(5)->and($job->backoff())->toHaveCount(4);
});

it('fails the job on a non-2xx response so it is retried', function () {
    Http::fake(['example.com/*' => Http::response('', 500)]);
    $endpoint = WebhookEndpoint::factory()->create();

    (new SendWebhookJob($endpoint, 'item.created', [], 'delivery-1'))->handle();
})->throws(Illuminate\Http\Client\RequestException::class);
