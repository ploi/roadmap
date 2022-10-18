<?php

namespace App\Observers;

use Mail;
use App\Models\Item;
use App\Models\User;
use App\Enums\ItemActivity;
use App\Services\WebhookClient;
use App\Settings\ColorSettings;
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
                if (!isset($receiver['type'])) {
                    continue;
                }

                match ($receiver['type']) {
                    'email' => Mail::to($receiver['webhook'])->send(new ItemHasBeenCreatedEmail($receiver, $item)),
                    'discord' => (new WebhookClient($receiver['webhook']))->send('POST', [
                        'username' => config('app.name'),
                        'avatar_url' => asset('storage/favicon.png'),
                        'embeds' => [
                            [
                                'title' => 'New roadmap item notification',
                                'description' => 'A new item with the title **' . $item->title . '** has been created',
                                'fields' => [
                                    [
                                        'name' => 'URL',
                                        'value' => route('items.show', $item),
                                    ],
                                ],
                                'color' => '2278750',
                            ],
                        ],
                    ]),
                    'slack' => (new WebhookClient($receiver['webhook']))->send('POST', [
                        'username' => config('app.name'),
                        'icon_url' => asset('storage/favicon.png'),
                        'attachments' => [
                            [
                                'fallback' => 'A new roadmap item has been created: <' . route('items.show', $item) . '|' . $item->title . '>',
                                'pretext' => 'A new roadmap item has been created: <' . route('items.show', $item) . '|' . $item->title . '>',
                                'color' => app(ColorSettings::class)->primary ?? '#2278750',
                                'fields' => [
                                    [
                                        'title' => $item->title,
                                        'value' => str($item->content)->limit(50),
                                        'shorts' => false,
                                    ]
                                ],
                            ],
                        ],
                    ])
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
        $item->comments()->delete();
        $item->changelogs()->detach();
    }
}
