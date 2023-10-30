<?php

namespace App\Observers;

use Mail;
use App\Models\Item;
use App\Models\User;
use App\Models\Project;
use App\Enums\ItemActivity;
use App\Settings\GeneralSettings;
use App\Jobs\SendWebhookForNewItemJob;
use Illuminate\Support\Facades\Storage;
use App\Mail\Admin\ItemHasBeenCreatedEmail;
use App\Notifications\Item\ItemUpdatedNotification;

class ItemObserver
{
    public function created(Item $item)
    {
        ItemActivity::createForItem($item, ItemActivity::Created);

        if ($receivers = app(GeneralSettings::class)->send_notifications_to) {
            foreach ($receivers as $receiver) {
                if (!isset($receiver['type'])) {
                    continue;
                }

                if (
                    isset($receiver['projects']) &&
                    Project::whereIn('id', $receiver['projects'])->count() &&
                    !in_array($item->project_id, $receiver['projects'])
                ) {
                    continue;
                }

                match ($receiver['type']) {
                    'email' => Mail::to($receiver['webhook'])->send(new ItemHasBeenCreatedEmail($receiver, $item)),
                    'discord', 'slack' => dispatch(new SendWebhookForNewItemJob($item, $receiver)),
                };
            }
        }
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

        $item->updateQuietly(['notify_subscribers' => true]);
    }

    public function deleting(Item $item)
    {
        try {
            Storage::delete('public/og-' . $item->slug . '-' . $item->id . '.jpg');
        } catch (\Throwable $exception) {
        }

        $item->votes()->delete();
        $item->parentComments()->delete();
        $item->comments()->delete();
        $item->changelogs()->detach();
    }
}
