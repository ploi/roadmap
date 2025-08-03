<?php

namespace App\Livewire;

use Filament\Forms;
use ResourceBundle;
use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\Http;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class Profile extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms, InteractsWithTable, InteractsWithActions;

    public $name;
    public $email;
    public $username;
    public $locale;
    public $per_page_setting;
    public $notification_settings;
    public $date_locale;
    public $hide_from_leaderboard;
    public User $user;

    public function mount(): void
    {
        $this->user = auth()->user();

        $this->form->fill([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'notification_settings' => $this->user->notification_settings,
            'per_page_setting' => $this->user->per_page_setting ?? [5],
            'locale' => $this->user->locale,
            'date_locale' => $this->user->date_locale,
            'hide_from_leaderboard' => $this->user->hide_from_leaderboard,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(trans('auth.profile'))
                ->columns()
                ->schema([
                    Forms\Components\TextInput::make('name')->label(trans('auth.name'))->required(),
                    Forms\Components\TextInput::make('username')
                        ->label(trans('profile.username'))
                        ->helperText(trans('profile.username_description'))
                        ->required()
                        ->rules([
                            'alpha_dash'
                        ])
                        ->unique(table: User::class, column: 'username', ignorable: auth()->user()),
                    Forms\Components\TextInput::make('email')->label(trans('auth.email'))->required()->email(),
                    Forms\Components\Select::make('locale')->label(trans('auth.locale'))->options($this->locales)->placeholder(trans('auth.locale_null_value')),
                    Forms\Components\Select::make('date_locale')->label(trans('auth.date_locale'))->options($this->locales)->placeholder(trans('auth.date_locale_null_value')),
                ])->collapsible(),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Section::make(trans('profile.notifications'))
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\CheckboxList::make('notification_settings')
                                ->label(trans('profile.notification_settings'))
                                ->options([
                                    'receive_mention_notifications' => trans('profile.receive_mention_notifications'),
                                    'receive_comment_reply_notifications' => trans('profile.receive_comment_reply_notifications'),
                                ]),
                        ])->collapsible(),

                    Forms\Components\Section::make(trans('profile.settings'))
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Select::make('per_page_setting')
                                                   ->label(trans('profile.per-page-setting'))
                                ->multiple()
                                ->options([
                                    5 => '5',
                                    10 => '10',
                                    15 => '15',
                                    25 => '25',
                                    50 => '50',
                                ])
                                ->required()
                                ->helperText(trans('profile.per-page-setting-helper'))
                                ->rules(['array', 'in:5,10,15,25,50']),

                            Forms\Components\Toggle::make('hide_from_leaderboard')
                                ->label(trans('profile.hide-from-leaderboard'))
                                ->helperText(trans('profile.hide-from-leaderboard-helper'))
                        ])->collapsible(),
                ])

        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $this->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'notification_settings' => $data['notification_settings'],
            'per_page_setting' => $data['per_page_setting'],
            'locale' => $data['locale'],
            'date_locale' => $data['date_locale'],
            'hide_from_leaderboard' => $data['hide_from_leaderboard'],
        ]);

        if ($this->user->wasChanged('locale', 'date_locale')) {
            Notification::make('profile')
                ->title('Profile')
                ->body('Refresh the page to show locale changes.')
                ->info()
                ->send();
        }

        Notification::make('profile-saved')
            ->title('Profile')
            ->body('Profile has been saved')
            ->success()
            ->send();
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('home');
    }

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->label(trans('profile.logout'))
            ->requiresConfirmation()
            ->modalAlignment(Alignment::Left)
            ->modalDescription('Are you sure you want to do this?')
            ->color(Color::Slate)
            ->action(fn () => $this->logout());
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(trans('profile.delete-account'))
            ->color(Color::Red)
            ->requiresConfirmation()
            ->modalAlignment(Alignment::Left)
            ->modalDescription('Are you sure you want to do this?')
            ->form([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->helperText('Enter your account\'s email address to delete your account')
                    ->in([auth()->user()->email])
            ])
            ->action(fn () => $this->delete());
    }

    public function delete()
    {
        auth()->user()->delete();

        auth()->logout();

        return redirect()->route('home');
    }

    public function getLocalesProperty(): array
    {
        $locales = ResourceBundle::getLocales('');

        return collect($locales)
            ->mapWithKeys(fn ($locale) => [$locale => $locale])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.profile', [
            'hasSsoLoginAvailable' => SsoProvider::isEnabled(),
        ]);
    }

    protected function getTableQuery(): Builder
    {
        return auth()->user()->userSocials()->latest()->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('provider'),
            Tables\Columns\TextColumn::make('created_at')->label('Date')->sortable()->dateTime(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('delete')
                ->action(function (Collection $records) {
                    foreach ($records as $record) {
                        $endpoint = config('services.sso.endpoints.revoke') ?? config('services.sso.url') . '/api/oauth/revoke';

                        $client = Http::withToken($record->access_token)->timeout(5);

                        if (config('services.sso.http_verify') === false) {
                            $client->withoutVerifying();
                        }

                        $client->delete($endpoint);

                        $record->delete();
                    }
                })
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash'),

        ];
    }
}
