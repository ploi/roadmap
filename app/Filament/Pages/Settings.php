<?php

namespace App\Filament\Pages;

use App\Models\Board;
use App\Enums\UserRole;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Enums\InboxWorkflow;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Services\GitHubService;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Enums\Alignment;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Schemas\Components\Utilities\Get;

class Settings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 1300;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.settings');
    }

    public function getHeading(): string|Htmlable
    {
        return trans('settings.title');
    }

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
            ->filter(
                function ($file) {
                    return Str::startsWith($file, 'og') && Str::endsWith($file, '.jpg');
                }
            );
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            [
            Tabs::make('main')
                ->persistTabInQueryString()
                ->schema(
                    [
                    Tab::make(trans('settings.general-title'))
                        ->schema(
                            [
                            Section::make('')
                                ->columns()
                                ->columnSpanFull()
                                ->schema(
                                    [
                                    Toggle::make('board_centered')
                                        ->label(trans('settings.general.center-boards'))
                                        ->helperText(trans('settings.general.center-boards-helper-text')),

                                    Toggle::make('show_projects_sidebar_without_boards')
                                        ->label(trans('settings.general.show-projects-sidebar-without-boards'))
                                        ->helperText(trans('settings.general.show-projects-sidebar-without-boards-helper-text')),

                                    Toggle::make('allow_general_creation_of_item')
                                        ->label(trans('settings.general.allow-general-creation-of-item'))
                                        ->helperText(trans('settings.general.allow-general-creation-of-item-helper-text')),

                                    Toggle::make('enable_item_age')
                                        ->label(trans('settings.general.enable-item-age'))
                                        ->helperText(trans('settings.general.enable-item-age-helper-text')),

                                    Toggle::make('show_voter_avatars')
                                        ->label(trans('settings.general.show-voters-avatars'))
                                        ->helperText(trans('settings.general.show-voters-avatars-helper-text')),

                                    Toggle::make('select_project_when_creating_item')
                                        ->label(trans('settings.general.select-project-creating-item'))
                                        ->helperText(trans('settings.general.select-project-creating-item-helper-text'))
                                        ->reactive(),

                                    Toggle::make('project_required_when_creating_item')
                                        ->label(trans('settings.general.project-required'))
                                        ->helperText(trans('settings.general.project-required-helper-text'))
                                        ->hidden(fn (Get $get) => $get('select_project_when_creating_item') === false),

                                    Toggle::make('select_board_when_creating_item')
                                        ->label(trans('settings.general.select-board-creat-item'))
                                        ->helperText(trans('settings.general.select-project-creating-item-helper-text'))
                                        ->reactive(),

                                    Toggle::make('board_required_when_creating_item')
                                        ->label(trans('settings.general.board-required'))
                                        ->helperText(trans('settings.general.board-required-helper-text'))
                                        ->hidden(fn (Get $get) => $get('select_board_when_creating_item') === false),

                                    Toggle::make('block_robots')
                                        ->label(trans('settings.general.block-robots'))
                                        ->helperText(trans('settings.general.block-robots-helper-text')),

                                    Toggle::make('users_must_verify_email')
                                        ->label(trans('settings.general.user-verify-email'))
                                        ->helperText(trans('settings.general.user-verify-email-helper-text')),

                                    Toggle::make('disable_file_uploads')
                                        ->label(trans('settings.general.disable-file-upload'))
                                        ->helperText(trans('settings.general.disable-file-upload-helper-text')),

                                    Toggle::make('disable_user_registration')
                                        ->label(trans('settings.general.disable-user-registration'))
                                        ->helperText(trans('settings.general.disable-user-registration-helper-text')),

                                    Toggle::make('show_github_link')
                                        ->label(trans('settings.general.show-github-link'))
                                        ->helperText(trans('settings.general.show-github-link-helper-text'))
                                        ->visible((new GitHubService)->isEnabled()),
                                    ]
                                ),


                            Grid::make()
                                ->columnSpanFull()
                                ->schema(
                                    [
                                Select::make('inbox_workflow')
                                    ->label(trans('settings.general.inbox-workflow'))
                                    ->helperText(trans('settings.general.inbox-workflow-helper-text'))
                                    ->options(InboxWorkflow::getSelectOptions())
                                    ->default(InboxWorkflow::WithoutBoardAndProject),

                                TextInput::make('password')
                                    ->label(trans('settings.general.roadmap-password'))
                                    ->helperText(trans('settings.general.roadmap-password-helper-text')),
                                ]
                                ),

                            RichEditor::make('welcome_text')
                                ->label(trans('settings.general.welcome-text'))
                                ->helperText(trans('settings.general.welcome-text-helper-text'))
                                ->columnSpan(2),
                            ]
                        ),

                    Tab::make(trans('settings.default-boards-title'))
                        ->schema(
                            [
                            Toggle::make('create_default_boards')
                                ->label(trans('settings.default-boards.create-default-boards'))
                                ->helperText(trans('settings.default-boards.create-default-boards-helper-text'))
                                ->reactive()
                                ->columnSpan(2),

                            Group::make(
                                [
                                Repeater::make('default_boards')
                                    ->label(trans('settings.default-boards-title'))
                                    ->columns()
                                    ->columnSpan(2)
                                    ->schema(
                                        [

                                        Grid::make()
                                            ->schema(
                                                [
                                                TextInput::make('title')
                                                    ->label(trans('settings.default-boards.title'))
                                                    ->helperText(trans('settings.default-boards.title-helper-text'))
                                                    ->required(),
                                                Select::make('sort_items_by')
                                                    ->label(trans('settings.default-boards.sort-by'))
                                                    ->helperText(trans('settings.default-boards.sort-by-helper-text'))
                                                    ->options(
                                                        [
                                                        Board::SORT_ITEMS_BY_POPULAR => trans('settings.default-boards.popular'),
                                                        Board::SORT_ITEMS_BY_LATEST => trans('settings.default-boards.latest'),
                                                        ]
                                                    )
                                                    ->default(Board::SORT_ITEMS_BY_POPULAR)
                                                    ->required(),
                                                ]
                                            ),

                                        Grid::make()->schema(
                                            [

                                            Toggle::make('visible')
                                                ->label(trans('settings.default-boards.visible'))
                                                ->helperText(trans('settings.default-boards.visible-helper-text'))
                                                ->default(true),

                                            Toggle::make('can_users_create')
                                                ->label(trans('settings.default-boards.can-users-create-items'))
                                                ->helperText(trans('settings.default-boards.can-users-create-items-helper-text')),

                                            Toggle::make('block_comments')
                                                ->label(trans('settings.default-boards.block-comments'))
                                                ->helperText(trans('settings.default-boards.block-comments-helper-text')),

                                            Toggle::make('block_votes')
                                                ->label(trans('settings.default-boards.block-votes'))
                                                ->helperText(trans('settings.default-boards.block-votes-helper-text')),

                                            ]
                                        ),

                                        Textarea::make('description')
                                            ->label(trans('settings.default-boards.description'))
                                            ->helperText(trans('settings.default-boards.description-helper-text'))
                                            ->columnSpan(2),

                                        ]
                                    ),
                                ]
                            )
                                ->columnSpan(2)
                                ->visible(fn (Get $get) => $get('create_default_boards')),
                            ]
                        ),

                    Tab::make(trans('settings.dashboard-items-title'))
                        ->schema(
                            [
                            Repeater::make('dashboard_items')
                                ->label(trans('settings.dashboard-items-title'))
                                ->columns()
                                ->columnSpan(2)
                                ->schema(
                                    [

                                    Select::make('type')
                                        ->label(trans('settings.dashboard-items.type'))
                                        ->helperText(trans('settings.dashboard-items.type-helper-text'))
                                        ->reactive()
                                        ->options(
                                            [
                                            'recent-items' => trans('settings.dashboard-items.recent-items'),
                                            'recent-comments' => trans('settings.dashboard-items.recent-comments'),
                                            'recent-activity' => trans('settings.dashboard-items.recent-activity'),
                                            'leaderboard' => trans('settings.dashboard-items.leaderboard'),
                                            'statistics' => trans('settings.dashboard-items.statistics')
                                            ]
                                        )
                                        ->default('recent-items'),

                                    Select::make('column_span')
                                        ->label(trans('settings.dashboard-items.column-span'))
                                        ->helperText(trans('settings.dashboard-items.column-span-helper-text'))
                                        ->options(
                                            [
                                                1 => 1,
                                                2 => 2,
                                            ]
                                        )
                                        ->default(1),

                                    Toggle::make('must_have_project')
                                        ->label(trans('settings.dashboard-items.must-have-project'))
                                        ->helperText(trans('settings.dashboard-items.must-have-project-helper-text'))
                                        ->reactive()
                                        ->visible(fn ($get) => $get('type') === 'recent-items'),

                                    Toggle::make('must_have_board')
                                        ->label(trans('settings.dashboard-items.must-have-board'))
                                        ->helperText(trans('settings.dashboard-items.must-have-board-helper-text'))
                                        ->visible(fn ($get) => $get('must_have_project') && $get('type') === 'recent-items'),

                                    ]
                                )
                                ->helperText(trans('settings.dashboard-items-helper-text')),
                            ]
                        ),

                    Tab::make(trans('settings.changelog-title'))
                        ->schema(
                            [

                            Toggle::make('enable_changelog')
                                ->label(trans('settings.changelog.enable-changelog'))
                                ->helperText(trans('settings.changelog.enable-changelog-helper-text'))
                                ->reactive()
                                ->columnSpan(2),

                            Toggle::make('show_changelog_author')
                                ->label(trans('settings.changelog.show-author'))
                                ->helperText(trans('settings.changelog.show-author-helper-text'))
                                ->visible(fn ($get) => $get('enable_changelog'))
                                ->columnSpan(2),

                            Toggle::make('show_changelog_related_items')
                                ->label(trans('settings.changelog.show-related-items'))
                                ->helperText(trans('settings.changelog.show-related-items-helper-text'))
                                ->visible(fn ($get) => $get('enable_changelog'))
                                ->columnSpan(2),

                            Toggle::make('show_changelog_like')
                                ->label(trans('settings.changelog.show-likes'))
                                ->helperText(trans('settings.changelog.show-likes-helper-text'))
                                ->visible(fn ($get) => $get('enable_changelog'))
                                ->columnSpan(2),
                            ]
                        ),

                    Tab::make(trans('settings.notifications-title'))
                        ->schema(
                            [
                            Repeater::make('send_notifications_to')
                                ->label(trans('settings.notifications.send-notifications-to'))
                                ->columns()
                                ->schema(
                                    [

                                    Select::make('type')
                                        ->label(trans('settings.notifications.type'))
                                        ->helperText(trans('settings.notifications.type-helper-text'))
                                        ->reactive()
                                        ->options(
                                            [
                                            'email' => 'E-mail',
                                            'discord' => 'Discord',
                                            'slack' => 'Slack'
                                              ]
                                        )
                                        ->default('email'),

                                    Select::make('projects')
                                        ->label(trans('settings.notifications.select-projects'))
                                        ->helperText(trans('settings.notifications.select-projects-helper-text'))
                                        ->multiple()
                                        ->options(Project::pluck('title', 'id')),

                                    TextInput::make('name')
                                        ->label(
                                            fn (Get $get) => match ($get('type')) {
                                                'email', null => trans('settings.notifications.name-receiver'),
                                                'discord', 'slack' => trans('settings.notifications.label'),
                                            }
                                        )
                                        ->helperText(trans('settings.notifications.name-helper-text'))
                                        ->required(),

                                    TextInput::make('webhook')
                                        ->label(
                                            fn (Get $get) => match ($get('type')) {
                                                'email', null => trans('settings.notifications.email'),
                                                'discord'=> trans('settings.notifications.discord-webhook-url'),
                                                'slack' => trans('settings.notifications.slack-webhook-url'),
                                            }
                                        )
                                        ->helperText(trans('settings.notifications.webhook-helper-text'))
                                        ->required()
                                        ->url(fn (Get $get) => in_array($get('type'), ['discord', 'slack']))
                                        ->email(fn (Get $get) => $get('type') === 'email'),
                                    ]
                                )
                                ->helperText(trans('settings.notifications-helper-text'))
                                ->columnSpan(2),
                            ]
                        ),

                    Tab::make(trans('settings.scripts-title'))
                        ->schema(
                            [
                            Textarea::make('custom_scripts')
                                ->label(trans('settings.scripts.custom-header-scripts'))
                                ->helperText(trans('settings.scripts.custom-header-scripts-helper-text'))
                                ->rows(10)
                                ->autosize()
                                ->columnSpan(2),
                            ]
                        ),

                    Tab::make(trans('settings.search-title'))
                        ->schema(
                            [
                            TagsInput::make('excluded_matching_search_words')
                                ->label(trans('settings.search.exclude-words'))
                                ->helperText(trans('settings.search.exclude-words-helper-text'))
                                ->columnSpan(2),
                            ]
                        ),

                    Tab::make(trans('settings.profanity-title'))
                        ->schema(
                            [
                            TagsInput::make('profanity_words')
                                ->label(trans('settings.profanity.profanity-filter'))
                                ->helperText(trans('settings.profanity.profanity-filter-helper-text'))
                            ]
                        )
                    ]
                )
                ->columns()
                ->columnSpan(2),
            ]
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('flush_og_images')
                ->action(
                    function () {
                        $items = $this->ogImages
                            ->each(
                                function ($file) {
                                    Storage::disk('public')->delete($file);
                                }
                            );

                        if ($items->count() === 0) {
                            Notification::make('cleared')
                                ->title(trans('settings.og.title'))
                                ->body(trans('settings.og.no-images-to-flush'))
                                ->success()
                                ->send();
                            return;
                        }

                        Notification::make('cleared')
                            ->title(trans('settings.og.title'))
                            ->body(sprintf(trans('settings.og.images-flushed'), $items->count()))
                            ->success()
                            ->send();

                        $this->ogImages = collect();
                    }
                )
                ->requiresConfirmation()
                ->disabled(!$this->ogImages->count())
                ->label(sprintf(trans('settings.og.label'), $this->ogImages->count()))
                ->color('gray')
                ->modalHeading(trans('settings.og.delete'))
                ->modalAlignment(Alignment::Left)
                ->modalDescription(sprintf(trans('settings.og.confirm'), $this->ogImages->count()))
        ];
    }
}
