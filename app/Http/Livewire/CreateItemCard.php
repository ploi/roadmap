<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class CreateItemCard extends Component implements HasForms
{
    use InteractsWithForms;

    public $project;

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required(),
            MarkdownEditor::make('content')
                ->required(),
        ];
    }

    public function submit()
    {
        $formState = $this->form->getState();

        $item = Item::create([
            ...$formState,
        ]);

        $item->user()->associate($this->project->boards()->first())->save();
        $item->board()->associate(auth()->user())->save();

        $item->toggleUpvote();

        return redirect()->route('projects.boards.show', [$this->project->id, $this->project->boards()->first()]);
    }

    public function render()
    {
        return view('livewire.create-item-card');
    }
}