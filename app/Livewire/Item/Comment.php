<?php

namespace App\Livewire\Item;

use App\View\Components\MarkdownEditor;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Arr;
use Livewire\Component;

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

    public function editAction()
    {
        return Action::make('edit')
            ->label(trans('comments.edit'))
            ->requiresConfirmation()
            ->modalAlignment(Alignment::Left)
            ->modalDescription('')
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->form(function (array $arguments) {
                return [
                    MarkdownEditor::make('content')->default(Arr::get($arguments, 'comment.content'))
                ];
            })
            ->link()
            ->action(function (array $data, array $arguments) {
                $comment = auth()->user()->comments()->findOrFail(Arr::get($arguments, 'comment.id'));

                $comment->update(['content' => Arr::get($data, 'content')]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }
}
