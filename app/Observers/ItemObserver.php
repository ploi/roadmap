<?php

namespace App\Observers;

use App\Mail\Admin\ItemHasBeenCreatedEmail;
use App\Models\Item;
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
}
