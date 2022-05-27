<?php

namespace App\Http\Livewire;

use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use LivewireUI\Modal\ModalComponent;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Forms\Concerns\InteractsWithForms;

class CreateItemModal extends ModalComponent implements HasForms
{
    use InteractsWithForms, CanNotify;

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        $inputs = [];

        $inputs[] = TextInput::make('title')
            ->minLength(3)
            ->required();

        if (app(GeneralSettings::class)->select_project_when_creating_item) {
            $inputs[] = Select::make('project_id')
                ->label('Project')
                ->reactive()
                ->options(Project::query()->pluck('title', 'id'));
        }

        // TODO This still bugs out atm (repeats the markdown editor for some reason)
        if (app(GeneralSettings::class)->select_board_when_creating_item) {
            $inputs[] = Select::make('board_id')
                ->label('Board')
                ->visible(fn($get) => $get('project_id'))
                ->options(Board::query()->pluck('title', 'id'));
        }

        $inputs[] = Group::make([
            MarkdownEditor::make('content')
                ->minLength(10)
                ->required()
        ]);


        return $inputs;
    }

    public function submit()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $data = $this->form->getState();

        $item = Item::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'project_id' => $data['project_id'] ?? null
        ]);

        $item->user()->associate(auth()->user())->save();

        $item->toggleUpvote();

        $this->closeModal();

        $this->notify('success', 'Item created');

        return route('items.show', $item->id);
    }

    public function render()
    {
        return view('livewire.create-item-modal');
    }

    public static function closeModalOnClickAway(): bool
    {
        return false;
    }

    public static function closeModalOnEscape(): bool
    {
        return false;
    }
}
