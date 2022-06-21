<?php

namespace App\Filament\Resources\ItemResource\Pages;

use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\ItemResource;
use Filament\Resources\Pages\EditRecord;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    public function getActions(): array
    {
        return [
            Action::make('view_public')->color('secondary')->url(fn () => route('items.show', $this->record)),
            Action::make('flush_og_images')
                ->action(function () {
                    Storage::disk('public')->delete('og-' . $this->record->slug . '-' . $this->record->id . '.jpg');

                    $this->notify('success', 'OG image removed 🎉');
                })
                ->label('Flush OG image')
                ->color('secondary')
                ->modalHeading('Delete OG image')
                ->modalSubheading('Are you sure you\'d like to delete the OG image for this item? This could be especially handy if you have changed branding color, if you feel this image is not correct.')
                ->requiresConfirmation(),
            ...parent::getActions()
        ];
    }
}
