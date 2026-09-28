<?php

namespace App\Livewire\Item;

use Livewire\Component;
use Filament\Actions\Action;
use App\Rules\ProfanityCheck;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use App\Models\Comment as CommentModel;
use App\View\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
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
                $comment = $this->findOwnComment($arguments['comment'] ?? null);

                return [
                    'content' => $comment->content,
                ];
            })
            ->form([
                MarkdownEditor::make('content')
                    ->rules([new ProfanityCheck()])
                    ->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                $comment = $this->findOwnComment($arguments['comment'] ?? null);
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
                MarkdownEditor::make('content')
                    ->rules([new ProfanityCheck()])
                    ->required()
            ])
            ->link()
            ->action(function (array $data, array $arguments): void {
                if (!auth()->check()) {
                    $this->redirectRoute('login');

                    return;
                }

                if (auth()->user()->needsToVerifyEmail()) {
                    Notification::make('must_verify')
                        ->title(trans('comments.reply'))
                        ->body('Please verify your email before replying to items.')
                        ->danger()
                        ->send();

                    $this->redirectRoute('verification.notice');

                    return;
                }

                $comment = $this->findVisibleComment($arguments['comment'] ?? null);

                abort_if((bool) $comment->item->board?->block_comments, 403);

                $comment->item->comments()->create([
                    'parent_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'content' => $data['content']
                ]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }

    /**
     * Find a comment owned by the current user.
     *
     * Action arguments are supplied by the client, so they can never be trusted to reference an owned comment.
     */
    protected function findOwnComment(mixed $commentId): CommentModel
    {
        return CommentModel::query()
            ->where('user_id', auth()->id())
            ->findOrFail((int) $commentId);
    }

    /**
     * Find a comment the current user is allowed to see, on an item the current user is allowed to see.
     *
     * Action arguments are supplied by the client, so they can never be trusted to reference a visible comment.
     */
    protected function findVisibleComment(mixed $commentId): CommentModel
    {
        return CommentModel::query()
            ->when(!auth()->user()?->hasAdminAccess(), fn (Builder $query) => $query->public())
            ->whereHas('item', fn (Builder $query) => $query->visibleForCurrentUser())
            ->findOrFail((int) $commentId);
    }
}
