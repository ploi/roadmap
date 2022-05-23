<?php

namespace App\Observers;

use App\Mail\Admin\ItemHasBeenCreatedEmail;
use App\Models\Board;
use App\Models\Item;
use App\Models\User;
use App\Notifications\Item\ItemUpdatedNotification;
use App\Settings\GeneralSettings;
use Mail;

class ItemObserver
{
    public function created(Item $item)
    {
        activity()
            ->performedOn($item)
            ->log('opened');

        if($receivers = app(GeneralSettings::class)->send_notifications_to){
            foreach($receivers as $receiver){
                Mail::to($receiver['email'])->send(new ItemHasBeenCreatedEmail($receiver, $item));
            }
        }
    }

    public function updating(Item $item)
    {
        $isDirty = false;

        if ($item->isDirty('board_id')) {
            activity()
                ->performedOn($item)
                ->log('moved item to board ' . $item->board->title);

            $isDirty = true;
        }

        if ($item->isDirty('project_id')) {
            activity()
                ->performedOn($item)
                ->log('moved item to project ' . $item->project->title);

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
        $item->votes()->delete();
        $item->comments()->delete();
    }
}
