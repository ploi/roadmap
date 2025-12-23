<?php

namespace App\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use App\View\Components\MarkdownEditor;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class Comment extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public $comments;
    public $comment;
    public $item;
    public $reply;

    public function render()
    {
        return view('livewire.item.comment');
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(trans('comments.edit'))
            ->requiresConfirmation()
            ->color(Color::Gray)
            ->modalAlignment(Alignment::Left)
            ->modalDescription('')
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->fillForm(function (array $arguments): array {
                $commentData = $arguments['comment'];
                $content = is_array($commentData) ? ($commentData['content'] ?? '') : ($commentData->content ?? '');
                return [
                    'content' => $content,
                ];
            })
            ->form([
                MarkdownEditor::make('content')
                    ->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                // Handle both array and object formats
                $commentData = $arguments['comment'];
                $commentId = is_array($commentData) ? $commentData['id'] : $commentData->id;

                $comment = auth()->user()->comments()->findOrFail($commentId);
                $comment->update(['content' => $data['content']]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }

    public function replyAction(): Action
    {
        return Action::make('reply')
            ->label(trans('comments.reply'))
            ->requiresConfirmation()
            ->color(Color::Gray)
            ->modalAlignment(Alignment::Left)
            ->modalDescription('')
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->form([
                MarkdownEditor::make('content')->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                // Handle both array and object formats
                $commentData = $arguments['comment'];
                $commentId = is_array($commentData) ? $commentData['id'] : $commentData->id;
                $itemId = is_array($commentData) ? $commentData['item_id'] : $commentData->item_id;

                $item = Item::findOrFail($itemId);
                $comment = $item->comments()->findOrFail($commentId);

                $item->comments()->create([
                    'parent_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'content' => $data['content']
                ]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }
}
