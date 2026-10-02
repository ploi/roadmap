<?php

namespace App\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use App\Rules\ProfanityCheck;
use App\Settings\GeneralSettings;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Contracts\HasForms;
use App\View\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class Comments extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public Item $item;
    public $content;
    public $private_content;

    protected $listeners = ['updatedComment' => '$refresh'];

    public function mount()
    {
        $this->form->fill();
    }

    public function submit()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        if (app(GeneralSettings::class)->users_must_verify_email && !auth()->user()->hasVerifiedEmail()) {
            Notification::make()
                ->title('Reply')
                ->body('Please verify your email before replying to items.');

            return redirect()->route('verification.notice');
        }

        $formState = array_merge($this->form->getState(), [
            'user_id' => auth()->id(),
        ]);

        if (filled($this->private_content)) {
            abort_unless(auth()->user()->hasAdminAccess(), 403);

            $formState['content'] = $this->private_content;
            $formState['private'] = true;
        }

        $this->item->comments()->create($formState);

        $this->content = '';
        $this->private_content = '';

        if ($this->item->project) {
            $this->redirectRoute('projects.items.show', [$this->item->project, $this->item]);
        }

        $this->redirectRoute('items.show', [$this->item]);
    }

    protected function getFormSchema(): array
    {
        if (auth()->user()?->hasAdminAccess()) {
            return [
                Tabs::make('')->tabs([
                    Tab::make(trans('comments.public-comment'))->schema([
                        MarkdownEditor::make('content')
                            ->label(trans('comments.public-comment'))
                            ->hiddenLabel()
                            ->mentions($this->item)
                            ->helperText(trans('comments.mention-helper-text'))
                            ->minLength(3)
                            ->rules(['required_if:private_content,null,""', 'prohibited_unless:private_content,null,""', new ProfanityCheck()]),
                    ])
                        ->icon('heroicon-o-globe-alt')
                        ->id('public'),

                    Tab::make(trans('comments.private-note'))->schema([
                        MarkdownEditor::make('private_content')
                            ->label(trans('comments.private-note'))
                            ->hiddenLabel()
                            ->mentions($this->item)
                            ->helperText(trans('comments.mention-helper-text'))
                            ->minLength(3)
                            ->visible(auth()->check() && auth()->user()->hasAdminAccess())
                            ->rules(['required_if:content,null,""', 'prohibited_unless:content,null,""', new ProfanityCheck()]),
                    ])
                        ->icon('heroicon-o-lock-closed')
                        ->extraAttributes(['class' => 'comment-tab-private'])
                        ->id('private'),
                ])->extraAttributes(['class' => 'comment-tabs']),
            ];
        }

        return [
            MarkdownEditor::make('content')
                ->rules([
                    new ProfanityCheck()
                ])
                ->label(trans('comments.comment'))
                ->helperText(trans('comments.mention-helper-text'))
                ->mentions($this->item)
                ->disableToolbarButtons(app(GeneralSettings::class)->getDisabledToolbarButtons())
                ->minLength(3)
                ->required(),
        ];
    }

    public function render()
    {
        $comments = $this->item
            ->comments()
            ->withWhereHas('user:id,name,email,username')
            ->with('votes.user:id,name,email,username')
            ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id')
            ->when(!auth()->user()?->hasAdminAccess(), fn ($query) => $query->where('private', false))
            ->get();

        $commentsById = $comments->keyBy('id');

        $comments->each(fn ($comment) => $comment->setRelation('parent', $commentsById->get($comment->parent_id)));

        return view('livewire.item.comments', [
            'comments' => $comments->mapToGroups(function ($comment) {
                return [(int)$comment->parent_id => $comment];
            }),
        ]);
    }
}
