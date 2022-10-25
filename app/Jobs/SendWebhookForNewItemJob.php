<?php

namespace App\Jobs;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use App\Services\WebhookClient;
use App\Settings\ColorSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWebhookForNewItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Item $item, private readonly array $receiver)
    {
    }

    public function handle()
    {
        (new WebhookClient($this->receiver['webhook']))->send('POST', $this->getPostDataForChannel());
    }

    private function getPostDataForChannel(): array
    {
        return match ($this->receiver['type']) {
            'discord' => [
                'username' => config('app.name'),
                'avatar_url' => asset('storage/favicon.png'),
                'embeds' => [
                    [
                        'title' => 'New roadmap item notification',
                        'description' => 'A new item with the title **' . $this->item->title . '** has been created',
                        'fields' => [
                            [
                                'name' => 'URL',
                                'value' => route('items.show', $this->item),
                            ],
                        ],
                        'color' => '2278750',
                    ],
                ],
            ],
            'slack' => [
                'username' => config('app.name'),
                'icon_url' => asset('storage/favicon.png'),
                'attachments' => [
                    [
                        'fallback' => 'A new roadmap item has been created: <' . route('items.show', $this->item) . '|' . $this->item->title . '>',
                        'pretext' => 'A new roadmap item has been created: <' . route('items.show', $this->item) . '|' . $this->item->title . '>',
                        'color' => app(ColorSettings::class)->primary ?? '#2278750',
                        'fields' => [
                            [
                                'title' => $this->item->title,
                                'value' => str($this->item->content)->limit(50),
                                'shorts' => false,
                            ]
                        ],
                    ],
                ],
            ],
        };
    }
}
