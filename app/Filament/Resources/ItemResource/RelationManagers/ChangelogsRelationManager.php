<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Tables\Table;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ChangelogResource;
use Filament\Resources\RelationManagers\RelationManager;

class ChangelogsRelationManager extends RelationManager
{
    protected static string $relationship = 'changelogs';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.changelog.label-plural');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return app(GeneralSettings::class)->enable_changelog;
    }

    public function table(Table $table): Table
    {
        return ChangelogResource::table($table);
    }

    protected function canCreate(): bool
    {
        return false;
    }
}
