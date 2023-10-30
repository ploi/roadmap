<?php

namespace App\Filament\Resources\CommentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
            DeleteAction::make(),
        ];
    }
}
