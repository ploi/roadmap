<?php

namespace App\Enums;

enum WebhookEvent: string
{
    case ItemCreated = 'item.created';
    case ItemUpdated = 'item.updated';
    case ItemStatusChanged = 'item.status_changed';
    case ItemDeleted = 'item.deleted';
    case CommentCreated = 'comment.created';
    case CommentUpdated = 'comment.updated';
    case CommentDeleted = 'comment.deleted';
    case ProjectCreated = 'project.created';
    case ProjectUpdated = 'project.updated';
    case ProjectDeleted = 'project.deleted';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $event) => [$event->value => $event->value])->all();
    }
}
