<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\ItemResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('view_public')->color('gray')->url(fn () => route('items.show', $this->record))->openUrlInNewTab(),
            Action::make('flush_og_images')
                ->action(function () {
                    Storage::disk('public')->delete('og-' . $this->record->slug . '-' . $this->record->id . '.jpg');

                    Notification::make('cleared')
                        ->title('OG images')
                        ->body('OG image removed 🎉')
                        ->success()
                        ->send();
                })
                ->label('Flush OG image')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Delete OG image')
                ->modalAlignment(Alignment::Left)
                ->modalDescription('Are you sure you\'d like to delete the OG image for this item? This could be especially handy if you have changed branding color, if you feel this image is not correct.'),
            Action::make('merge item')
                ->color('warning')
                ->action(function (array $data): void {
                    /** @var Item $selectedItem */
                    $selectedItem = Item::query()->find($data['item_id']);

                    if (!$selectedItem->hasVoted($this->record->user)) {
                        $selectedItem->toggleUpvote($this->record->user);
                    }

                    $selectedItem->comments()->create([
                        'user_id' => auth()->id(),
                        'content' => "**Merged {$this->record->title} into this item** \n\n Created by: {$this->record->user->name} \n\n {$this->record->content}",
                        'private' => $data['private'],
                    ]);

                    $this->record->comments()->update([
                        'item_id' => $selectedItem->id,
                    ]);

                    $this->record->assignedUsers()->detach();
                    $this->record->delete();

                    Notification::make('merging')
                        ->title('Merging')
                        ->body("Merged {$this->record->title} into {$selectedItem->title}")
                        ->success()
                        ->send();

                    $this->redirect(ItemResource::getUrl());
                })
                ->form([
                    Select::make('item_id')
                        ->label('Item')
                        ->options(Item::query()->whereNot('id', $this->record->id)->pluck('title', 'id'))
                        ->required()
                        ->searchable(),
                    Toggle::make('private')
                        ->label('As private comment?')
                        ->default(true),
                ])
                ->modalDescription('Select the item you want to merge it with. This action cannot be undone')
                ->modalSubmitActionLabel('Merge with selected and delete current item'),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
