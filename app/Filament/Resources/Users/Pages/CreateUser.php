<?php

namespace App\Filament\Resources\Users\Pages;

use App\Mail\UserCreated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $plainPassword = Str::password(16, letters: true, numbers: true, symbols: true);

        $data['password'] = Hash::make($plainPassword);

        $this->plainPassword = $plainPassword;

        return $data;
    }

    protected function afterCreate(): void
    {
        $loginUrl = route('login');

        Mail::to($this->record->email)->send(
            new UserCreated(
                user: $this->record,
                password: $this->plainPassword,
                loginUrl: $loginUrl
            )
        );
    }

    private string $plainPassword;
}
