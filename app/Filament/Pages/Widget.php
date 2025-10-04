<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Enums\UserRole;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use App\Settings\WidgetSettings;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

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
        return 'Widget';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Feedback Widget';
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
        return $schema->components(
            [
                Section::make('Widget Configuration')
                    ->description('Configure the feedback widget that can be embedded on external websites.')
                    ->columnSpanFull()
                    ->schema(
                        [
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

                            TagsInput::make('allowed_domains')
                                ->label('Allowed Domains')
                                ->helperText('Restrict widget usage to specific domains (e.g., example.com). Leave empty to allow all domains.')
                                ->placeholder('example.com'),
                        ]
                    )->columns(),

                Section::make('Embed Code')
                    ->description('Copy and paste this code into your website to enable the feedback widget.')
                    ->columnSpanFull()
                    ->schema(
                        [
                            Placeholder::make('embed_code')
                                ->label('JavaScript Embed Code')
                                ->content(fn () => new HtmlString(
                                    '<div class="relative">
                                        <pre id="widget-embed-code" class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-4 rounded-md overflow-x-auto text-sm leading-relaxed font-mono">' .
                                    htmlspecialchars($this->getEmbedCode()) .
                                    '</pre>
                                        <button
                                            type="button"
                                            onclick="
                                                const code = document.getElementById(\'widget-embed-code\').textContent;
                                                navigator.clipboard.writeText(code).then(() => {
                                                    this.textContent = \'Copied!\';
                                                    setTimeout(() => { this.innerHTML = \'<svg class=\\\'w-4 h-4 mr-1\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\\\'></path></svg> Copy\'; }, 2000);
                                                });
                                            "
                                            class="absolute top-2 right-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-1"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Copy
                                        </button>
                                    </div>'
                                ))
                                ->helperText('Click the copy button to copy this code and paste it before the closing </body> tag on your website.'),
                        ]
                    ),
            ]
        );
    }

    protected function getEmbedCode(): string
    {
        $url = url('/widget.js');
        return <<<HTML
<script>
  (function() {
    const script = document.createElement('script');
    script.src = '{$url}';
    script.async = true;
    document.body.appendChild(script);

    // Optional: Pre-fill user information
    // Wait for \$roadmap to be available
    script.onload = function() {
      // Uncomment and customize these lines to pre-fill user data:
      // \$roadmap.setName('John Doe');
      // \$roadmap.setEmail('john@example.com');
    };
  })();
</script>
HTML;
    }
}
