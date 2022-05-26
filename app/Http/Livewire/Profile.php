<?php

namespace App\Http\Livewire;

use App\Models\UserSocial;
use Filament\Forms;
use App\Models\User;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Filament\Tables;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Forms\Concerns\InteractsWithForms;

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
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Profile')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('username')
                    ->helperText('This username will be used to mention your name in comments.')
                    ->required()
                    ->unique(table: User::class, column: 'username', ignorable: auth()->user()),
                Forms\Components\TextInput::make('email')->required()->email(),
            ])->collapsible(),

            Forms\Components\Section::make('Notifications')
                ->schema([
                    Forms\Components\CheckboxList::make('notification_settings')
                        ->options([
                            'receive_mention_notifications' => 'Receive mention notifications',
                        ]),
                ])->collapsible()
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
        ]);

        $this->notify('success', 'Profile has been saved.');
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.profile', [
            'hasSsoLoginAvailable' => $this->hasSsoLoginAvailable()
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
                        Http::withToken($record->access_token)
                            ->timeout(5)
                            ->delete(config('services.sso.url') . '/api/oauth/revoke');

                        $record->delete();
                    }
                })
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash')

        ];
    }

    protected function hasSsoLoginAvailable()
    {
        return config('services.sso.url') &&
            config('services.sso.client_id') &&
            config('services.sso.client_secret') &&
            config('services.sso.redirect');
    }
}
