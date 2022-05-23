<?php

namespace App\Http\Livewire\Item;

use App\View\Components\MarkdownEditor;
use Filament\Forms;
use App\Models\Item;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Xetaio\Mentions\Parser\MentionParser;

class Comments extends Component implements HasForms
{
    use InteractsWithForms;

    public Item $item;
    public $comments;
    public $content;

    public function mount()
    {
        $this->form->fill([]);
    }

    public function submit()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $formState = $this->form->getState();

        $comment = $this->item->comments()->create($formState);
        $comment->user()->associate(auth()->user());
        $comment->save();

        $parser = new MentionParser($comment);
        $content = $parser->parse($comment->content);
        $comment->content = $content;
        $comment->save();

        $this->content = '';
    }

    protected function getFormSchema(): array
    {
        return [
            MarkdownEditor::make('content')
                ->required()
                ->minLength(3),
        ];
    }

    public function render()
    {
        $this->comments = $this->item->comments()->with('user:id,name,email')->oldest()->get();

        return view('livewire.item.comments');
    }
}
