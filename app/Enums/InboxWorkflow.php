<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum InboxWorkflow: int
{
    case Disabled = 0;
    case WithoutBoardAndProject = 1;
    case WithoutBoard = 2;

    public static function getSelectOptions(): Collection
    {
        return collect(InboxWorkflow::cases())
            ->mapWithKeys(fn (InboxWorkflow $inboxWorkflow) => [$inboxWorkflow->value => $inboxWorkflow->label()]);
    }

    public function label(): string
    {
        return match ($this) {
            self::Disabled               => 'Disabled',
            self::WithoutBoardAndProject => 'Items without board and project',
            self::WithoutBoard           => 'Items without board',
        };
    }
}
