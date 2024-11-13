<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Models\Comment;
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
                  ->label(trans('resources.item.view-public'))
                  ->color('gray')
                  ->openUrlInNewTab()
                  ->url(fn () => route('items.show', $this->getCurrentComment()->item) . '#comment-' . $this->getCurrentComment()->id),
            DeleteAction::make(),
        ];
    }

    protected function getCurrentComment(): Comment
    {
        /** @var Comment */
        return $this->record;
    }
}
