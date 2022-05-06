<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Filament\Facades\Filament;
use LivewireUI\Modal\ModalComponent;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class CreateItemModal extends ModalComponent implements HasForms
{
    use InteractsWithForms;

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
        $data = $this->form->getState();

        $item = Item::create([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        $item->user()->associate(auth()->user())->save();

        $item->toggleUpvote();

        $this->closeModal();

        Filament::notify('success', 'Item created');
    }

    public function render()
    {
        return view('livewire.create-item-modal');
    }
}
