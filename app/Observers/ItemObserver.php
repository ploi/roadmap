<?php

namespace App\Observers;

use Mail;
use App\Models\Item;
use App\Models\User;
use App\Enums\ItemActivity;
use App\Settings\GeneralSettings;
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
                Mail::to($receiver['email'])->send(new ItemHasBeenCreatedEmail($receiver, $item));
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

        if ($isDirty) {
            $users = $item->subscribedVotes()->with('user')->get()->pluck('user');

            $users->each(function (User $user) use ($item) {
                $user->notify(new ItemUpdatedNotification($item));
            });
        }
    }

    public function deleting(Item $item)
    {
        try {
            Storage::delete('public/og-' . $item->slug . '-' . $item->id . '.jpg');
        } catch (\Throwable $exception) {
        }

        $item->votes()->delete();
        $item->comments()->delete();
    }
}
