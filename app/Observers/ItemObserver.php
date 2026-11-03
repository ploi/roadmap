<?php

namespace App\Observers;

use Mail;
use Throwable;
use WeakMap;
use App\Models\Item;
use App\Models\User;
use App\Models\Project;
use App\Enums\ItemActivity;
use App\Enums\WebhookEvent;
use App\Services\WebhookDispatcher;
use App\Http\Resources\Api\V1\ItemResource;
use App\Settings\GeneralSettings;
use App\Jobs\SendWebhookForNewItemJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\Admin\ItemHasBeenCreatedEmail;
use App\Notifications\Item\ItemUpdatedNotification;

class ItemObserver
{
    /**
     * Items that are being created and not committed yet, their follow-up saves must not send `item.updated`.
     * Static, because Laravel resolves a new observer instance for every model event.
     *
     * @var WeakMap<Item, true>|null
     */
    private static ?WeakMap $pendingCreation = null;

    public function creating(Item $item): void
    {
        self::$pendingCreation ??= new WeakMap();
        self::$pendingCreation[$item] = true;
    }

    public function created(Item $item)
    {
        ItemActivity::createForItem($item, ItemActivity::Created);

        // Creating an item takes several saves (slug, user, project, board, vote). Wrap them in a transaction
        // and the webhook is sent once, with the complete item, after the commit.
        DB::afterCommit(function () use ($item) {
            unset(self::$pendingCreation[$item]);

            WebhookDispatcher::dispatch(WebhookEvent::ItemCreated, fn () => $this->webhookData($item->fresh()));
        });

        if ($item->isPrivate()) {
            return;
        }

        if ($receivers = app(GeneralSettings::class)->send_notifications_to) {
            foreach ($receivers as $receiver) {
                if (!isset($receiver['type'])) {
                    continue;
                }

                $isLimitedToProjects = isset($receiver['projects']) &&
                    Project::whereIn('id', $receiver['projects'])->count();

                if ($isLimitedToProjects && !in_array($item->project_id, $receiver['projects'])) {
                    continue;
                }

                // Items in a private project only notify receivers that explicitly selected that project.
                if ($item->project?->private && !$isLimitedToProjects) {
                    continue;
                }

                match ($receiver['type']) {
                    'email' => Mail::to($receiver['webhook'])->send(new ItemHasBeenCreatedEmail($receiver, $item)),
                    'discord', 'slack' => dispatch(new SendWebhookForNewItemJob($item, $receiver)),
                };
            }
        }
    }

    public function deleted(Item $item): void
    {
        WebhookDispatcher::dispatch(WebhookEvent::ItemDeleted, fn () => $this->webhookData($item));
    }

    public function updated(Item $item): void
    {
        // Vote counts change on every vote, consumers can read them from the API.
        $changes = array_diff_key($item->getChanges(), array_flip(['updated_at', 'notify_subscribers', 'total_votes']));

        if ($changes === [] || isset(self::$pendingCreation[$item])) {
            return;
        }

        WebhookDispatcher::dispatch(WebhookEvent::ItemUpdated, fn () => $this->webhookData($item));

        if (array_key_exists('board_id', $changes)) {
            WebhookDispatcher::dispatch(WebhookEvent::ItemStatusChanged, fn () => $this->webhookData($item) + [
                'previous_board_id' => $item->getOriginal('board_id'),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function webhookData(Item $item): array
    {
        return (new ItemResource($item->load(['board', 'project'])))->resolve();
    }

    public function updating(Item $item)
    {
        $isDirty = false;

        if ($item->isDirty('board_id') && $item->board) {
            ItemActivity::createForItem($item, ItemActivity::MovedToBoard, [
                'board' => $item->board->title,
            ]);

            $isDirty = true;
        }

        if ($item->isDirty('project_id') && $item->project) {
            ItemActivity::createForItem($item, ItemActivity::MovedToProject, [
                'project' => $item->project->title,
            ]);

            $isDirty = true;
        }

        if ($item->isDirty('pinned') && $item->pinned) {
            ItemActivity::createForItem($item, ItemActivity::Pinned);

            $isDirty = true;
        }

        if ($item->isDirty('pinned') && !$item->pinned) {
            ItemActivity::createForItem($item, ItemActivity::Unpinned);

            $isDirty = true;
        }

        if ($item->isDirty('private') && $item->private) {
            ItemActivity::createForItem($item, ItemActivity::MadePrivate);

            $isDirty = true;
        }

        if ($item->isDirty('private') && !$item->private) {
            ItemActivity::createForItem($item, ItemActivity::MadePublic);

            $isDirty = true;
        }

        if ($item->isDirty('issue_number') && !$item->issue_number) {
            ItemActivity::createForItem($item, ItemActivity::LinkedToIssue, [
                'issue_number' => $item->issue_number,
                'repo' => $item->project->repo,
            ]);
        }

        if ($isDirty && $item->notify_subscribers) {
            $users = $item->subscribedVotes()->with('user')->get()->pluck('user');

            $users->each(function (User $user) use ($item) {
                $user->notify(new ItemUpdatedNotification($item));
            });
        }

        // Part of the update that is being saved.
        $item->notify_subscribers = true;
    }

    public function deleting(Item $item)
    {
        try {
            Storage::delete('public/og-' . $item->slug . '-' . $item->id . '.jpg');
        } catch (Throwable $exception) {
        }

        $item->votes()->delete();
        $item->parentComments()->delete();
        $item->comments()->delete();
        $item->changelogs()->detach();
    }
}
