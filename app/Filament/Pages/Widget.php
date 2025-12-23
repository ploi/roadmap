<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use Filament\Schemas\Schema;
use App\Settings\WidgetSettings;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use App\Settings\ActivityWidgetSettings;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;

class Widget extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-code-bracket';

    protected static string $settings = WidgetSettings::class;

    protected static ?int $navigationSort = 1500;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return 'Widgets';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Widgets';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Embeddable widgets that can be added to external websites. Configure settings and copy the embed code to integrate with any site. These settings are saved separately. Click "Save" at the bottom after making changes.';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->hasRole(UserRole::Admin), 403);
    }

    public function form(Schema $schema): Schema
    {
        // Since SettingsPage only supports one settings class,
        // we'll handle the activity widget settings with a custom save method
        return $schema->components([
            Tabs::make('main')
                ->persistTabInQueryString()
                ->schema([
                    Tab::make('Feedback Widget')
                        ->schema([
                            Section::make('Widget Configuration')
                                ->description('Configure the feedback widget that can be embedded on external websites.')
                                ->columnSpanFull()
                                ->schema([
                                    Toggle::make('enabled')
                                        ->label('Enable Widget')
                                        ->helperText('Enable or disable the feedback widget'),

                                    Select::make('position')
                                        ->label('Widget Position')
                                        ->options([
                                            'bottom-right' => 'Bottom Right',
                                            'bottom-left' => 'Bottom Left',
                                            'top-right' => 'Top Right',
                                            'top-left' => 'Top Left',
                                        ])
                                        ->default('bottom-right')
                                        ->required(),

                                    ColorPicker::make('primary_color')
                                        ->label('Primary Color')
                                        ->default('#2563EB'),

                                    TextInput::make('button_text')
                                        ->label('Button Text')
                                        ->default('Feedback')
                                        ->required(),

                                    Toggle::make('hide_button')
                                        ->label('Hide Button')
                                        ->helperText('Hide the default floating button. Use $roadmap.open() to open the modal programmatically.'),

                                    TagsInput::make('allowed_domains')
                                        ->label('Allowed Domains')
                                        ->helperText('Restrict widget usage to specific domains (e.g., example.com). Leave empty to allow all domains.')
                                        ->placeholder('example.com'),
                                ])->columns(),

                            Section::make('Embed Code')
                                ->description('Copy and paste this code into your website to enable the feedback widget.')
                                ->columnSpanFull()
                                ->schema([
                                    Placeholder::make('embed_code')
                                        ->label('JavaScript Embed Code')
                                        ->content(fn () => new HtmlString(
                                            '<div class="relative">
                                                <pre id="feedback-widget-embed-code" class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-4 rounded-md overflow-x-auto text-sm leading-relaxed font-mono">' .
                                            htmlspecialchars($this->getFeedbackEmbedCode()) .
                                            '</pre>
                                                <button
                                                    type="button"
                                                    onclick="
                                                        const code = document.getElementById(\'feedback-widget-embed-code\').textContent;
                                                        navigator.clipboard.writeText(code).then(() => {
                                                            this.textContent = \'Copied!\';
                                                            setTimeout(() => { this.innerHTML = \'<svg class=\\\'w-4 h-4 mr-1\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\\\'></path></svg> Copy\'; }, 2000);
                                                        });
                                                    "
                                                    class="absolute top-4 right-2 px-3 py-1.5 bg-transparent border-0 rounded-md text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition-all flex items-center gap-1 cursor-pointer"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Copy
                                                </button>
                                            </div>'
                                        ))
                                        ->helperText('Click the copy button to copy this code and paste it before the closing </body> tag on your website.'),
                                ]),
                        ]),

                    Tab::make('Activity Widget')
                        ->schema([
                            Section::make('Widget Configuration')
                                ->description('Configure the activity widget that displays recent activity from your roadmap.')
                                ->columnSpanFull()
                                ->schema([
                                    Toggle::make('activity_enabled')
                                        ->label('Enable Widget')
                                        ->helperText('Enable or disable the activity widget')
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->enabled)),

                                    Select::make('activity_position')
                                        ->label('Widget Position')
                                        ->options([
                                            'bottom-right' => 'Bottom Right',
                                            'bottom-left' => 'Bottom Left',
                                            'top-right' => 'Top Right',
                                            'top-left' => 'Top Left',
                                        ])
                                        ->default('bottom-left')
                                        ->required()
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->position)),

                                    ColorPicker::make('activity_primary_color')
                                        ->label('Primary Color')
                                        ->default('#2563EB')
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->primary_color)),

                                    TextInput::make('activity_button_text')
                                        ->label('Button Text')
                                        ->default('Recent activity')
                                        ->required()
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->button_text)),

                                    TextInput::make('activity_modal_title')
                                        ->label('Modal Title')
                                        ->default('Recent activity')
                                        ->required()
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->modal_title)),

                                    TextInput::make('activity_items_limit')
                                        ->label('Items Limit')
                                        ->helperText('Maximum number of items to display (max 50)')
                                        ->numeric()
                                        ->default(10)
                                        ->minValue(1)
                                        ->maxValue(50)
                                        ->required()
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->items_limit)),

                                    Toggle::make('activity_hide_button')
                                        ->label('Hide Button')
                                        ->helperText('Hide the default floating button. Use $roadmapActivity.open() to open the modal programmatically.')
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->hide_button)),

                                    TagsInput::make('activity_allowed_domains')
                                        ->label('Allowed Domains')
                                        ->helperText('Restrict widget usage to specific domains (e.g., example.com). Leave empty to allow all domains.')
                                        ->placeholder('example.com')
                                        ->afterStateHydrated(fn ($component) => $component->state(app(ActivityWidgetSettings::class)->allowed_domains)),
                                ])->columns(),

                            Section::make('Embed Code')
                                ->description('Copy and paste this code into your website to enable the activity widget.')
                                ->columnSpanFull()
                                ->schema([
                                    Placeholder::make('activity_embed_code')
                                        ->label('JavaScript Embed Code')
                                        ->content(fn () => new HtmlString(
                                            '<div class="relative">
                                                <pre id="activity-widget-embed-code" class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-4 rounded-md overflow-x-auto text-sm leading-relaxed font-mono">' .
                                            htmlspecialchars($this->getActivityEmbedCode()) .
                                            '</pre>
                                                <button
                                                    type="button"
                                                    onclick="
                                                        const code = document.getElementById(\'activity-widget-embed-code\').textContent;
                                                        navigator.clipboard.writeText(code).then(() => {
                                                            this.textContent = \'Copied!\';
                                                            setTimeout(() => { this.innerHTML = \'<svg class=\\\'w-4 h-4 mr-1\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\\\'></path></svg> Copy\'; }, 2000);
                                                        });
                                                    "
                                                    class="absolute top-4 right-2 px-3 py-1.5 bg-transparent border-0 rounded-md text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition-all flex items-center gap-1 cursor-pointer"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Copy
                                                </button>
                                            </div>'
                                        ))
                                        ->helperText('Click the copy button to copy this code and paste it before the closing </body> tag on your website.'),
                                ]),
                        ]),
                ])->columns()->columnSpan(2),
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Save activity widget settings separately
        $activitySettings = app(ActivityWidgetSettings::class);
        $activitySettings->enabled = $data['activity_enabled'] ?? false;
        $activitySettings->position = $data['activity_position'] ?? 'bottom-left';
        $activitySettings->primary_color = $data['activity_primary_color'] ?? '#2563EB';
        $activitySettings->button_text = $data['activity_button_text'] ?? 'Recent activity';
        $activitySettings->modal_title = $data['activity_modal_title'] ?? 'Recent activity';
        $activitySettings->items_limit = $data['activity_items_limit'] ?? 10;
        $activitySettings->hide_button = $data['activity_hide_button'] ?? false;
        $activitySettings->allowed_domains = $data['activity_allowed_domains'] ?? [];
        $activitySettings->save();

        // Remove activity fields from feedback widget data
        return array_filter($data, fn ($key) => !str_starts_with($key, 'activity_'), ARRAY_FILTER_USE_KEY);
    }

    protected function getFeedbackEmbedCode(): string
    {
        $url = url('/widget.js');
        return <<<HTML
<script>
  (function() {
    const script = document.createElement('script');
    script.src = '{$url}';
    script.async = true;
    document.body.appendChild(script);

    // Optional: Configure widget
    script.onload = function() {
      // Pre-fill user data:
      // \$roadmap.setName('John Doe');
      // \$roadmap.setEmail('john@example.com');

      // If you hid the default button, use your own to trigger:
      // document.getElementById('my-feedback-btn').onclick = function() {
      //   \$roadmap.open();
      // };
    };
  })();
</script>
HTML;
    }

    protected function getActivityEmbedCode(): string
    {
        $url = url('/activity-widget.js');
        return <<<HTML
<script>
  (function() {
    const script = document.createElement('script');
    script.src = '{$url}';
    script.async = true;
    document.body.appendChild(script);

    // Optional: Configure widget
    script.onload = function() {
      // If you hid the default button, use your own to trigger:
      // document.getElementById('my-activity-btn').onclick = function() {
      //   \$roadmapActivity.open();
      // };
    };
  })();
</script>
HTML;
    }
}
