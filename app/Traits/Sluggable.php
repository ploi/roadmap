<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function bootSluggable(): void
    {
        static::created(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->id . ' ' . $model->title);
                $model->save();
            }
        });
    }
}
