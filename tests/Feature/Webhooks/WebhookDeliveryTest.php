<?php

use App\Models\Item;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Project;
use App\Jobs\SendWebhookJob;
use App\Enums\WebhookEvent;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\DB;
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
    Http::fake(['1.1.1.1/*' => Http::response('', 200)]);
    $endpoint = WebhookEndpoint::factory()->create(['secret' => 'top-secret']);

    $job = new SendWebhookJob($endpoint, WebhookEvent::ItemCreated->value, ['id' => 1], 'delivery-1', now()->toIso8601String());
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
    Http::fake(['1.1.1.1/*' => Http::response('', 500)]);
    $endpoint = WebhookEndpoint::factory()->create();

    config(['queue.default' => 'database']);

    (new SendWebhookJob($endpoint, 'item.created', [], 'delivery-1', now()->toIso8601String()))->handle();
})->throws(Illuminate\Http\Client\RequestException::class);

it('does not call addresses that are not public', function () {
    Http::fake();
    config(['queue.default' => 'database']);

    foreach (['https://127.0.0.1/hook', 'https://169.254.169.254/latest', 'http://1.1.1.1/hook'] as $url) {
        $endpoint = WebhookEndpoint::factory()->create(['url' => $url]);

        expect(fn () => (new SendWebhookJob($endpoint, 'item.created', [], 'd', now()->toIso8601String()))->handle())
            ->toThrow(RuntimeException::class);
    }

    Http::assertNothingSent();
});

it('does not let a failing endpoint break the request on the sync queue', function () {
    Http::fake(['1.1.1.1/*' => Http::response('', 500)]);
    $endpoint = WebhookEndpoint::factory()->create();

    (new SendWebhookJob($endpoint, 'item.created', [], 'd', now()->toIso8601String()))->handle();

    Http::assertSentCount(1);
});

it('sends one complete item.created for an item that is created in a transaction', function () {
    $project = Project::factory()->create();
    $board = Board::factory()->create(['project_id' => $project->id]);
    $user = createUser();

    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create();

    DB::transaction(function () use ($project, $board, $user) {
        $item = Item::create(['title' => 'Idea', 'content' => 'Content']);
        $item->user()->associate($user)->save();
        $item->project()->associate($project)->save();
        $item->board()->associate($board)->save();
        $item->toggleUpvote($user);
    });

    expect(dispatchedEvents())->toBe(['item.created']);

    Bus::assertDispatched(SendWebhookJob::class, fn (SendWebhookJob $job) => $job->data['user_id'] === $user->id
        && $job->data['project_id'] === $project->id
        && $job->data['board_id'] === $board->id
        && $job->data['total_votes'] === 1);
});

it('does not send item.updated for vote count changes', function () {
    $item = Item::factory()->create();

    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create();

    $item->toggleUpvote(createUser());

    Bus::assertNotDispatched(SendWebhookJob::class);
});

it('sends the comment as stored, after mentions are linked', function () {
    $jane = createUser(['username' => 'jane']);

    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create(['events' => ['comment.created']]);

    $comment = Comment::factory()->create(['content' => 'thanks @jane']);

    Bus::assertDispatched(SendWebhookJob::class, fn (SendWebhookJob $job) => $job->data['content'] === $comment->fresh()->content);
});

it('sends project and comment lifecycle events', function () {
    Bus::fake([SendWebhookJob::class]);
    WebhookEndpoint::factory()->create();

    $project = Project::factory()->create();
    $comment = Comment::factory()->create();
    $comment->update(['content' => 'Edited']);
    $comment->delete();
    $project->delete();

    expect(dispatchedEvents())->toEqualCanonicalizing([
        'project.created', 'comment.created', 'comment.updated', 'comment.deleted', 'project.deleted',
    ]);
});
