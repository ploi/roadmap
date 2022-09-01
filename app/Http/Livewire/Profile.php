<?php

namespace App\Http\Livewire;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class Profile extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable, CanNotify;

    public $name;
    public $email;
    public User $user;

    public function mount(): void
    {
        $this->user = auth()->user();

        $this->form->fill([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'notification_settings' => $this->user->notification_settings,
            'date_time_format' => $this->user->date_time_format,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(trans('auth.profile'))->schema([
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
            ])->collapsible(),

            Forms\Components\Section::make(trans('profile.notifications'))
                ->schema([
                    Forms\Components\CheckboxList::make('notification_settings')->label(trans('profile.notification_settings'))
                        ->options([
                            'receive_mention_notifications' => trans('profile.receive_mention_notifications'),
                            'receive_comment_reply_notifications' => trans('profile.receive_comment_reply_notifications'),
                        ]),
                ])->collapsible(),

            Forms\Components\Section::make(trans('profile.settings'))
                ->schema([
                    Forms\Components\Select::make('date_time_format')->label(trans('profile.date_format'))
                        ->options(
                            [
                                'Y-m-d H:i:s' => now()->format('Y-m-d H:i:s'),
                                'm-d-Y H:i:s' => now()->format('m-d-Y H:i:s'),
                                'd-m-Y H:i:s' => now()->format('d-m-Y H:i:s'),
                                'Y-m-d h:i:s A' => now()->format('Y-m-d h:i:s A'),
                                'm-d-Y h:i:s A' => now()->format('m-d-Y h:i:s A'),
                                'd-m-Y h:i:s A' => now()->format('d-m-Y h:i:s A'),
                            ]
                        ),
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
            'date_time_format' => $data['date_time_format']
        ]);

        $this->notify('success', 'Profile has been saved.');
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('home');
    }

    public function logoutConfirm()
    {
        $this->dispatchBrowserEvent('open-modal', ['id' => 'logoutConfirm']);
    }

    public function closeLogoutConfirm()
    {
        $this->dispatchBrowserEvent('close-modal', ['id' => 'logoutConfirm']);
    }

    public function deleteConfirm()
    {
        $this->dispatchBrowserEvent('open-modal', ['id' => 'deleteAccount']);
    }

    public function closeDeleteConfirm()
    {
        $this->dispatchBrowserEvent('close-modal', ['id' => 'deleteAccount']);
    }

    public function delete()
    {
        auth()->user()->delete();

        auth()->logout();

        return redirect()->route('home');
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
