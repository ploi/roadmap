<?php

namespace App\Filament\Resources\CommentResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CommentResource;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('view_public')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn () => route('items.show', $this->record->item) . '#comment-' . $this->record->id),
            ...parent::getActions()
        ];
    }
}
