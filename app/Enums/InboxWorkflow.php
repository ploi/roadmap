<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum InboxWorkflow: string
{
    case Disabled = 'disabled';
    case WithoutBoardAndProject = 'without-board-and-project';
    case WithoutBoardOrProject = 'without-board-or-project';
    case WithoutBoard = 'without-board';

    /**
     * @return Collection<string, string>
     */
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
            self::WithoutBoardOrProject => 'Items without board or project',
            self::WithoutBoard           => 'Items without board',
        };
    }
}
