<?php

namespace App\Filament\Pages;

use Closure;
use SebastiaanKloos\FilamentCodeEditor\Components\CodeEditor;
use Storage;
use Illuminate\Support\Str;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class Settings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    public Collection $ogImages;

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->hasRole('admin'), 403);

        $this->ogImages = collect(Storage::disk('public')->allFiles())
            ->filter(function ($file) {
                return Str::startsWith($file, 'og') && Str::endsWith($file, '.jpg');
            });
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('main')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            Toggle::make('board_centered')->label('Center boards in project views')
                                ->helperText('When centering, this will always show the boards in the center of the content area.')
                                ->columnSpan(2),

                            Toggle::make('create_default_boards')->label('Create default boards for new projects')
                                ->helperText('When creating a new project, some default boards can be created.')
                                ->reactive()
                                ->columnSpan(2),

                            Group::make([
                                TagsInput::make('default_boards')->label('Default boards')
                                    ->placeholder('Enter defaults to be created upon project creation')
                                    ->helperText('These boards will automatically be prefilled when you create a project.')
                                    ->columnSpan(2),
                            ])
                                ->visible(fn ($get) => $get('create_default_boards')),

                            Toggle::make('show_projects_sidebar_without_boards')->label('Show projects in sidebar without boards')
                                ->helperText('If you don\'t want to show projects without boards in the sidebar, toggle this off.')
                                ->columnSpan(2),

                            Toggle::make('allow_general_creation_of_item')->label('Allow general creation of an item')
                                ->helperText('This allows your users to create an item without a board.')
                                ->columnSpan(2),

                            Toggle::make('enable_item_age')
                                ->label('Enable item age')
                                ->helperText('Enable this to show the age of an item on the details page.')
                                ->columnSpan(2),

                            Toggle::make('select_project_when_creating_item')
                                ->label('Users can select a project when creating an item')
                                ->columnSpan(2)
                                ->reactive(),

                            Toggle::make('project_required_when_creating_item')
                                  ->label('Project is required when creating an item')
                                  ->hidden(fn (Closure $get) => $get('select_project_when_creating_item') === false)
                                  ->columnSpan(2),

                            Toggle::make('select_board_when_creating_item')
                                ->label('Users can select a board when creating an item')
                                ->columnSpan(2)
                                ->reactive(),

                            Toggle::make('board_required_when_creating_item')
                                ->label('Board is required when creating an item')
                                ->hidden(fn (Closure $get) => $get('select_board_when_creating_item') === false)
                                ->columnSpan(2),

                            TextInput::make('password')->helperText('Entering a password here will ask your users to enter a password before entering the roadmap.'),

                            RichEditor::make('welcome_text')
                                ->columnSpan(2)
                                ->helperText('This content will show at the top of the dashboard for (for all users).'),
                        ]),

                    Tabs\Tab::make('Dashboard items')
                        ->schema([
                            Repeater::make('dashboard_items')
                                ->columnSpan(2)
                                ->schema([
                                    Select::make('type')
                                        ->reactive()
                                        ->options([
                                            'recent-items' => 'Recent items',
                                            'recent-comments' => 'Recent comments'
                                        ])->default('recent-items'),
                                    Select::make('column_span')->options([
                                        1 => 1,
                                        2 => 2,
                                    ])->default(1),
                                    Toggle::make('must_have_board')
                                        ->reactive()
                                        ->visible(fn ($get) => $get('type') === 'recent-items')
                                        ->helperText('Enable this to show items that have a board'),
                                    Toggle::make('must_have_project')
                                        ->visible(fn ($get) => $get('must_have_board') && $get('type') === 'recent-items')
                                        ->helperText('Enable this to show items that have a project'),
                                ])->helperText('Determine which items you want to show on the dashboard (for all users).'),
                        ]),

                    Tabs\Tab::make('Notifications')
                        ->schema([
                            Repeater::make('send_notifications_to')
                                ->schema([
                                    TextInput::make('name')->required(),
                                    TextInput::make('email')->required()->email(),
                                ])
                                ->helperText('This will send email notifications once a new item has been created.')
                                ->columnSpan(2),
                        ]),

                    Tabs\Tab::make('SEO')
                        ->schema([
                            Toggle::make('block_robots')
                                ->helperText('Instructs your roadmap to add the block robots META tag, it\'s up to the search engines to honor this request.')
                        ]),

                    Tabs\Tab::make('Scripts')
                        ->schema([
                            CodeEditor::make('custom_scripts')
                                ->label('Custom header script')
                                ->helperText('This allows you to add your own custom widget, or tracking tool. Code inside here will always be placed inside the head section.')
                                ->columnSpan(2),
                        ])
                ])
                ->columns()
                ->columnSpan(2),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('flush_og_images')
                ->action(function () {
                    $items = $this->ogImages
                        ->each(function ($file) {
                            Storage::disk('public')->delete($file);
                        });

                    if ($items->count() === 0) {
                        $this->notify('primary', 'There are no OG images to flush ✅');
                        return;
                    }

                    $this->notify('success', 'Flushed ' . $items->count() . ' OG image(s) 🎉');

                    $this->ogImages = collect();
                })
                ->disabled(!$this->ogImages->count())
                ->label('Flush OG images (' . $this->ogImages->count() . ')')
                ->color('secondary')
                ->modalHeading('Delete OG images')
                ->modalSubheading('Are you sure you\'d like to delete all the OG images? There\'s currently ' . $this->ogImages->count() . ' image(s) in the storage. This could be especially handy if you have changed branding color, if you feel some images are not correct.')
                ->requiresConfirmation(),
        ];
    }
}
