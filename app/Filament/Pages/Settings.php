<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Storage;
use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use App\Enums\InboxWorkflow;
use App\Services\GitHubService;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class Settings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    public Collection $ogImages;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->hasRole(UserRole::Admin), 403);

        $this->ogImages = collect(Storage::disk('public')->allFiles())
            ->filter(function ($file) {
                return Str::startsWith($file, 'og') && Str::endsWith($file, '.jpg');
            });
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('main')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            Section::make('')
                                ->columns(2)
                                ->schema([
                                    Toggle::make('board_centered')->label('Center boards in project views')
                                        ->helperText('When centering, this will always show the boards in the center of the content area.')
                                        ->columnSpan(1),

                                    Toggle::make('show_projects_sidebar_without_boards')->label('Show projects in sidebar without boards')
                                        ->helperText('If you don\'t want to show projects without boards in the sidebar, toggle this off.')
                                        ->columnSpan(1),

                                    Toggle::make('allow_general_creation_of_item')->label('Allow general creation of an item')
                                        ->helperText('This allows your users to create an item without a board.')
                                        ->columnSpan(1),

                                    Toggle::make('enable_item_age')
                                        ->label('Enable item age')
                                        ->helperText('Enable this to show the age of an item on the details page.')
                                        ->columnSpan(1),

                                    Toggle::make('show_voter_avatars')
                                        ->label('Enable voter avatars when viewing an item')
                                        ->helperText('Enabling this will show the avatars of the most recent voters when viewing an item.')
                                        ->columnSpan(1),

                                    Toggle::make('select_project_when_creating_item')
                                        ->label('Users can select a project when creating an item')
                                        ->columnSpan(1)
                                        ->reactive(),

                                    Toggle::make('project_required_when_creating_item')
                                        ->label('Project is required when creating an item')
                                        ->hidden(fn (\Filament\Forms\Get $get) => $get('select_project_when_creating_item') === false)
                                        ->columnSpan(1),

                                    Toggle::make('select_board_when_creating_item')
                                        ->label('Users can select a board when creating an item')
                                        ->columnSpan(1)
                                        ->reactive(),

                                    Toggle::make('board_required_when_creating_item')
                                        ->label('Board is required when creating an item')
                                        ->hidden(fn (\Filament\Forms\Get $get) => $get('select_board_when_creating_item') === false)
                                        ->columnSpan(1),

                                    Toggle::make('users_must_verify_email')
                                        ->label('Users must verify their email before they can submit items, or reply to items.')
                                        ->columnSpan(1),

                                    Toggle::make('disable_file_uploads')
                                        ->label('Disallow users to upload files or images via the markdown editors.')
                                        ->columnSpan(1),

                                    Toggle::make('show_github_link')
                                        ->label('Show a link to the linked GitHub issue on the item page')
                                        ->columnSpan(1)
                                        ->visible((new GitHubService)->isEnabled()),
                                ]),


                            Grid::make()->schema([
                                Select::make('inbox_workflow')
                                    ->options(InboxWorkflow::getSelectOptions())
                                    ->default(InboxWorkflow::WithoutBoardAndProject)
                                    ->helperText('This allows you to change which items show up in the inbox in the sidebar.'),
                            ]),

                            TextInput::make('password')->helperText('Entering a password here will ask your users to enter a password before entering the roadmap.'),

                            RichEditor::make('welcome_text')
                                ->columnSpan(2)
                                ->helperText('This content will show at the top of the dashboard for (for all users).'),
                        ]),

                    Tabs\Tab::make('Default boards')
                        ->schema([
                            Toggle::make('create_default_boards')->label('Create default boards for new projects')
                                ->helperText('When creating a new project, some default boards can be created.')
                                ->reactive()
                                ->columnSpan(2),

                            Group::make([
                                Repeater::make('default_boards')
                                    ->columns(2)
                                    ->columnSpan(2)
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('title')->required(),
                                            Select::make('sort_items_by')
                                                ->options([
                                                    Board::SORT_ITEMS_BY_POPULAR => 'Popular',
                                                    Board::SORT_ITEMS_BY_LATEST => 'Latest',
                                                ])
                                                ->default(Board::SORT_ITEMS_BY_POPULAR)
                                                ->required(),
                                        ]),
                                        Grid::make(2)->schema([
                                            Toggle::make('visible')->default(true)->helperText('Hides the board from the public view, but will still be accessible if you use the direct URL.'),
                                            Toggle::make('can_users_create')->helperText('Allow users to create items in this board.'),
                                            Toggle::make('block_comments')->helperText('Block users from commenting to items in this board.'),
                                            Toggle::make('block_votes')->helperText('Block users from voting to items in this board.'),
                                        ]),

                                        Textarea::make('description')->helperText('Used as META description for SEO purposes.')->columnSpan(2),

                                    ]),
                            ])->columnSpan(2)->visible(fn ($get) => $get('create_default_boards')),
                        ]),

                    Tabs\Tab::make('Dashboard items')
                        ->schema([
                            Repeater::make('dashboard_items')
                                ->columns(2)
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
                                    Toggle::make('must_have_project')
                                        ->reactive()
                                        ->visible(fn ($get) => $get('type') === 'recent-items')
                                        ->helperText('Enable this to show items that have a project'),
                                    Toggle::make('must_have_board')
                                        ->visible(fn ($get) => $get('must_have_project') && $get('type') === 'recent-items')
                                        ->helperText('Enable this to show items that have a board'),
                                ])->helperText('Determine which items you want to show on the dashboard (for all users).'),
                        ]),

                    Tabs\Tab::make('Changelog')
                        ->schema([
                            Toggle::make('enable_changelog')
                                ->reactive()
                                ->label('Enable changelog in the roadmap')
                                ->columnSpan(2),
                            Toggle::make('show_changelog_author')
                                ->label('Show the author of the changelog.')
                                ->visible(fn ($get) => $get('enable_changelog'))
                                ->columnSpan(2),
                            Toggle::make('show_changelog_related_items')
                                ->label('Show the related items on the changelog.')
                                ->visible(fn ($get) => $get('enable_changelog'))
                                ->columnSpan(2),
                        ]),

                    Tabs\Tab::make('Notifications')
                        ->schema([
                            Repeater::make('send_notifications_to')
                                ->columns(4)
                                ->schema([
                                    Select::make('type')
                                        ->default('email')
                                        ->reactive()
                                        ->options([
                                            'email' => 'E-mail',
                                            'discord' => 'Discord',
                                            'slack' => 'Slack'
                                        ]),
                                    Select::make('projects')
                                        ->multiple()
                                        ->helperText('Optionally select projects to trigger for, if you do not select a project it will always notify on new events')
                                        ->options(Project::pluck('title', 'id')),
                                    TextInput::make('name')->label(function ($get) {
                                        return match ($get('type')) {
                                            'email' => 'Name receiver',
                                            'discord', 'slack' => 'Label',
                                            null => 'Name receiver' // Fallback for previous roadmap users
                                        };
                                    })->required(),
                                    TextInput::make('webhook')
                                        ->label(function ($get) {
                                            return match ($get('type')) {
                                                'email' => 'E-mail',
                                                'discord' => 'Discord webhook URL',
                                                'slack' => 'Slack webhook URL',
                                                null => 'E-mail' // Fallback for previous roadmap users
                                            };
                                        })
                                        ->required()
                                        ->url(function ($get) {
                                            return $get('type') === 'discord' || $get('type') === 'slack';
                                        })
                                        ->email(function ($get) {
                                            return $get('type') === 'email';
                                        }),
                                ])
                                ->helperText('This will send email notifications once a new item has been created or when there is a new version of the roadmap software.')
                                ->columnSpan(2),
                        ]),

                    Tabs\Tab::make('SEO')
                        ->schema([
                            Toggle::make('block_robots')
                                ->helperText('Instructs your roadmap to add the block robots META tag, it\'s up to the search engines to honor this request.')
                        ]),

                    Tabs\Tab::make('Scripts')
                        ->schema([
                            Textarea::make('custom_scripts')
                                ->label('Custom header script')
                                ->helperText('This allows you to add your own custom widget, or tracking tool. Code inside here will always be placed inside the head section.')
                                ->columnSpan(2),
                        ]),
                    Tabs\Tab::make('Excluded search words')
                        ->schema([
                            TagsInput::make('excluded_matching_search_words')
                                ->placeholder('New excluded word')
                                ->helperText('Define any words here that should be excluded when users create a new item, you can also add words in your own language here to be excluded. Defining words here will increase the search results when a user starts creating an item, to prevent duplicates.')
                        ]),
                    Tabs\Tab::make('Profanity')
                        ->schema([
                            TagsInput::make('profanity_words')
                                ->placeholder('Words')
                                ->helperText('Add words here that should be filtered out when users create items or comment on items.')
                        ])
                ])
                ->columns()
                ->columnSpan(2),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('flush_og_images')
                ->action(function () {
                    $items = $this->ogImages
                        ->each(function ($file) {
                            Storage::disk('public')->delete($file);
                        });

                    if ($items->count() === 0) {
                        Notification::make('cleared')
                            ->title('OG images')
                            ->body('There are no OG images to flush ✅')
                            ->success()
                            ->send();
                        return;
                    }

                    Notification::make('cleared')
                        ->title('OG images')
                        ->body('Flushed ' . $items->count() . ' OG image(s) 🎉')
                        ->success()
                        ->send();

                    $this->ogImages = collect();
                })
                ->requiresConfirmation()
                ->disabled(!$this->ogImages->count())
                ->label('Flush OG images (' . $this->ogImages->count() . ')')
                ->color('gray')
                ->modalHeading('Delete OG images')
                ->modalAlignment(Alignment::Left)
                ->modalDescription('Are you sure you\'d like to delete all the OG images? There\'s currently ' . $this->ogImages->count() . ' image(s) in the storage. This could be especially handy if you have changed branding color, if you feel some images are not correct.')
        ];
    }
}
