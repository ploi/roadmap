<?php

namespace App\Livewire\Item;

use Livewire\Component;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use App\Models\Comment as CommentModel;
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
                $comment = CommentModel::findOrFail($arguments['comment']);
                return [
                    'content' => $comment->content,
                ];
            })
            ->form([
                MarkdownEditor::make('content')
                    ->mentions($this->item)
                    ->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                $comment = auth()->user()->comments()->findOrFail($arguments['comment']);
                $comment->update(['content' => $data['content']]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(trans('comments.delete'))
            ->requiresConfirmation()
            ->color(Color::Gray)
            ->modalIcon('heroicon-o-trash')
            ->modalIconColor('danger')
            ->modalHeading(trans('comments.delete-comment'))
            ->modalDescription(trans('comments.delete-comment-description'))
            ->modalSubmitAction(fn (Action $action) => $action->color('danger')->label(trans('comments.delete')))
            ->link()
            ->visible(fn (): bool => (bool) auth()->user()?->hasAdminAccess())
            ->action(function (array $arguments): void {
                $comment = CommentModel::findOrFail($arguments['comment']);
                $comment->delete();

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
                MarkdownEditor::make('content')->mentions($this->item)->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                $comment = CommentModel::findOrFail($arguments['comment']);

                $comment->item->comments()->create([
                    'parent_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'content' => $data['content']
                ]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }
}
