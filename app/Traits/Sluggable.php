<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function bootSluggable()
    {
        static::created(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->id . ' ' . $model->title);
                $model->save();
            }
        });
    }
}
