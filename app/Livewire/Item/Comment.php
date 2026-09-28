<?php

namespace App\Livewire\Item;

use Livewire\Component;
use App\Rules\ProfanityCheck;
use Filament\Actions\Action;
use App\Settings\GeneralSettings;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
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

    public bool $isReplying = false;
    public $replyContent;

    public function render()
    {
        return view('livewire.item.comment');
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(trans('comments.edit'))
            ->color(Color::Gray)
            ->modalHeading(trans('comments.edit-comment'))
            ->modalWidth(Width::TwoExtraLarge)
            ->modalSubmitActionLabel(trans('general.save'))
            ->fillForm(function (array $arguments): array {
                $comment = CommentModel::findOrFail($arguments['comment']);
                return [
                    'content' => $comment->content,
                ];
            })
            ->form([
                MarkdownEditor::make('content')
                    ->hiddenLabel()
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

    /**
     * Opens the inline reply editor below this comment, or sends guests to the login page.
     */
    public function replyAction(): Action
    {
        return Action::make('reply')
            ->label(trans('comments.reply'))
            ->color(Color::Gray)
            ->link()
            ->url(fn (): ?string => auth()->check() ? null : route('login', ['intended' => $this->item->view_url]))
            ->action(function (): void {
                $this->form->fill();
                $this->isReplying = true;
            });
    }

    public function cancelReply(): void
    {
        $this->isReplying = false;
        $this->form->fill();
    }

    public function submitReply()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (app(GeneralSettings::class)->users_must_verify_email && ! auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        abort_if($this->item->board?->block_comments, 403);

        $reply = $this->item->comments()->create([
            'parent_id' => $this->comment->id,
            'user_id' => auth()->id(),
            'content' => $this->form->getState()['replyContent'],
        ]);

        return redirect()->to($this->item->view_url . '#comment-' . $reply->id);
    }

    protected function getFormSchema(): array
    {
        return [
            MarkdownEditor::make('replyContent')
                ->label(trans('comments.reply'))
                ->hiddenLabel()
                ->mentions($this->item)
                ->disableToolbarButtons(app(GeneralSettings::class)->getDisabledToolbarButtons())
                ->minLength(3)
                ->required()
                ->rules([new ProfanityCheck()]),
        ];
    }
}
