<?php

namespace App\Filament\Resources\Projects\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Projects\ProjectResource;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('view_public')
                ->label(trans('resources.project.view-public'))
                ->color('gray')->url(fn () => route('projects.show', $this->record)),

            DeleteAction::make(),
        ];
    }
}
