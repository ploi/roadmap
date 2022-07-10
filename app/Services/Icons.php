<?php

namespace App\Services;

use BladeUI\Icons\Factory;
use Illuminate\Support\Collection;

class Icons
{
    public static function all(): Collection
    {
        $sets = collect(app(Factory::class)->all());

        return $sets->map(function ($set) {
            return collect($set['paths'])->map(function ($path) use ($set) {
                return collect(scandir($path))
                    ->filter(fn ($file) => !is_dir($file))
                    ->map(function ($item) use ($set) {
                        return $set['prefix'] . '-' . str_replace('.svg', '', $item);
                    });
            })->flatten();
        })
        ->flatten()
        ->mapWithKeys(fn ($item) => [$item => $item]);
    }
}
