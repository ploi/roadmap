<?php

namespace App\Enums;

enum WebhookEvent: string
{
    case ItemCreated = 'item.created';
    case ItemUpdated = 'item.updated';
    case ItemStatusChanged = 'item.status_changed';
    case ItemDeleted = 'item.deleted';
    case CommentCreated = 'comment.created';
    case ProjectUpdated = 'project.updated';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $event) => [$event->value => $event->value])->all();
    }
}
