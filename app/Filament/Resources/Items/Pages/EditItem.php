<?php

namespace App\Filament\Resources\Items\Pages;

use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Items\ItemResource;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('view_public')
                ->label(trans('resources.item.view-public'))
                ->color('gray')
                ->url(fn () => route('items.show', $this->record))
                ->openUrlInNewTab(),

            Action::make('flush_og_images')
                ->label(trans('settings.og.flush-single'))
                ->action(
                    function () {
                        Storage::disk('public')->delete('og-' . $this->record->slug . '-' . $this->record->id . '.jpg');

                        Notification::make('cleared')
                            ->title(trans('settings.og.title'))
                            ->body(trans('settings.og.image-flushed'))
                            ->success()
                            ->send();
                    }
                )
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(trans('settings.og.delete-single'))
                ->modalAlignment(Alignment::Left)
                ->modalDescription(trans('settings.og.confirm-single')),

            Action::make('merge item')
                ->label(trans('resources.item.merge'))
                ->color('warning')
                ->action(
                    function (array $data): void {
                        /**
                         * @var Item $selectedItem
                         */
                        $selectedItem = Item::query()->find($data['item_id']);

                        if (!$selectedItem->hasVoted($this->record->user)) {
                            $selectedItem->toggleUpvote($this->record->user);
                        }

                        $selectedItem->comments()->create(
                            [
                                'user_id' => auth()->id(),
                                'content' => sprintf(trans('resources.item.merged-content'), $this->record->title, $this->record->user->name, $this->record->content),
                                'private' => $data['private'],
                            ]
                        );

                        $this->record->comments()->update(
                            [
                                'item_id' => $selectedItem->id,
                            ]
                        );

                        $this->record->assignedUsers()->detach();
                        $this->record->delete();

                        Notification::make('merging')
                            ->title(trans('resources.item.merging'))
                            ->body(sprintf(trans('resources.item.merged-message'), $this->record->title, $selectedItem->title))
                            ->success()
                            ->send();

                        $this->redirect(ItemResource::getUrl());
                    }
                )
                ->schema([
                    Select::make('item_id')
                        ->label(trans('resources.item.label'))
                        ->options(Item::query()->whereNot('id', $this->record->id)->pluck('title', 'id'))
                        ->required()
                        ->searchable(),

                    Toggle::make('private')
                        ->label(trans('resources.item.private-comment'))
                        ->default(true),
                ])
                ->modalDescription(trans('resources.item.merge-helper-text'))
                ->modalSubmitActionLabel(trans('resources.item.merge-submit')),
            DeleteAction::make()->modalAlignment(Alignment::Left),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
