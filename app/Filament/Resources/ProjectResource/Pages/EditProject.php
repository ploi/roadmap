<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function getActions(): array
    {
        return [
            Action::make('view_public')->color('secondary')->url(fn() => route('projects.show', $this->record)),
            ...parent::getActions()
        ];
    }
}
