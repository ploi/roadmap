<?php

namespace App\Livewire\Item;

use Filament\Notifications\Notification;
use function auth;
use function view;
use App\Models\Item;
use App\Models\Board;
use App\Models\Project;
use Livewire\Component;
use App\Settings\GeneralSettings;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class Create extends Component implements HasForms
{
    use InteractsWithForms;

    public Project|null $project = null;
    public Board|null $board = null;

    public $title;
    public $content;

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label(trans('table.title'))
                ->minLength(3)
                ->required(),
            MarkdownEditor::make('content')
                ->label(trans('table.content'))
                ->disableToolbarButtons(app(GeneralSettings::class)->getDisabledToolbarButtons())
                ->minLength(10)
                ->required(),
        ];
    }

    public function submit()
    {
        if (!$this->board->canUsersCreateItem()) {
            Notification::make('items')
                ->title('Items')
                ->body(trans('items.not-allowed-to-create-items'))
                ->danger()
                ->send();

            $this->redirectRoute('projects.boards.show', [$this->project, $this->board]);
            return;
        }

        $formState = $this->form->getState();

        $item = Item::create([
            ...$formState,
        ]);

        $item->user()->associate(auth()->user())->save();
        $item->project()->associate($this->project)->save();
        $item->board()->associate($this->board)->save();

        $item->toggleUpvote();

        Notification::make('items')
            ->title('Items')
            ->body(trans('items.item_created'))
            ->success()
            ->send();

        $this->redirectRoute('projects.items.show', [$this->project, $item]);
    }

    public function render()
    {
        return view('livewire.item.create');
    }
}
