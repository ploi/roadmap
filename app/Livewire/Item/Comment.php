<?php

namespace App\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Illuminate\Support\Arr;
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
        $this->comments = $this->item
            ->comments()
            ->with('user:id,name,email')
            ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id')
            ->when(!auth()->user()?->hasAdminAccess(), fn ($query) => $query->where('private', false))
            ->get()
            ->mapToGroups(function ($comment) {
                return [(int)$comment->parent_id => $comment];
            });

        return view('livewire.item.comment');
    }

    public function editAction()
    {
        return Action::make('edit')
            ->label(trans('comments.edit'))
            ->requiresConfirmation()
            ->color(Color::Gray)
            ->modalAlignment(Alignment::Left)
            ->modalDescription('')
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->form(function (array $arguments) {
                return [
                    MarkdownEditor::make('content')
                        ->default(Arr::get($arguments, 'comment.content'))
                    ->required()
                ];
            })
            ->link()
            ->action(function (array $data, array $arguments) {
                $comment = auth()->user()->comments()->findOrFail(Arr::get($arguments, 'comment.id'));

                $comment->update(['content' => Arr::get($data, 'content')]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }

    public function replyAction()
    {
        return Action::make('reply')
            ->label(trans('comments.reply'))
            ->requiresConfirmation()
            ->color(Color::Gray)
            ->modalAlignment(Alignment::Left)
            ->modalDescription('')
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->form(function () {
                return [
                    MarkdownEditor::make('content')->required()
                ];
            })
            ->link()
            ->action(function (array $data, array $arguments) {
                $item = Item::findOrFail(Arr::get($arguments, 'comment.item_id'));

                $comment = $item->comments()->findOrfail(Arr::get($arguments, 'comment.id'));

                $item->comments()->create([
                    'parent_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'content' => Arr::get($data, 'content')
                ]);

                $this->redirectRoute('items.show', $comment->item->slug);
            });
    }
}
