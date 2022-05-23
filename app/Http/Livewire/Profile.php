<?php

namespace App\Http\Livewire;

use Filament\Forms;
use App\Models\User;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Forms\Concerns\InteractsWithForms;

class Profile extends Component implements HasForms
{
    use InteractsWithForms, CanNotify;

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
            Forms\Components\Card::make([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('username')
                    ->helperText('This username will be used to mention your name in comments.')
                    ->required()
                    ->unique(table: User::class, column: 'username', ignorable: auth()->user()),
                Forms\Components\TextInput::make('email')->required()->email(),
            ]),

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\CheckboxList::make('notification_settings')
                        ->options([
                            'receive_mention_notifications' => 'Receive mention notifications',
                        ]),
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
        return view('livewire.profile');
    }
}
