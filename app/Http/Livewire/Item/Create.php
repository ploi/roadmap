<?php

namespace App\Http\Livewire\Item;

use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Livewire\Concerns\CanNotify;
use Livewire\Component;
use function auth;
use function redirect;
use function view;

class Create extends Component implements HasForms
{
    use InteractsWithForms, CanNotify;

    public Project $project;
    public Board $board;

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->minLength(3)
                ->required(),
            MarkdownEditor::make('content')
                ->minLength(10)
                ->required(),
        ];
    }

    public function submit()
    {
        $formState = $this->form->getState();

        $item = Item::create([
            ...$formState,
        ]);

        $item->user()->associate(auth()->user())->save();
        $item->board()->associate($this->board)->save();

        $item->toggleUpvote();

        $this->notify('success', 'Item has been created!');

        return redirect()->route('projects.boards.show', [$this->project->id, $this->board->id]);
    }

    public function render()
    {
        return view('livewire.item.create');
    }
}
