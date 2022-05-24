<?php

namespace App\Traits;

use App\Services\Tailwind;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait HasOgImage
{
    public function getOgImage($withDescription = false, $subject = 'Roadmap')
    {
        $pathToImage = storage_path('app/public/og-' . $this->slug . '-' . $this->id . '.jpg');

        if (!File::exists($pathToImage)) {
            try {
                $tailwind = new Tailwind('brand', app(\App\Settings\ColorSettings::class)->primary);

                $wordCount = str_word_count($this->title);
                $y = 270;

                if ($wordCount > 9) {
                    $y = 240;
                }

                $img = Image::make(public_path('images/og-template.jpg'));
                if ($subject) {
                    $img->text(wordwrap($subject, 22, PHP_EOL), 78, $y - 65, function ($font) use ($tailwind) {
                        $font->file(public_path('fonts/Lexend-Bold.ttf'));
                        $font->size(25);
                        $font->color($tailwind->shades['brand-500']);
                    });
                }

                $img->text(wordwrap(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding(Str::limit($this->title, 15), "UTF-8"))), 22, PHP_EOL), 75, $y, function ($font) {
                    $font->file(public_path('fonts/Lexend-Bold.ttf'));
                    $font->size(60);
                    $font->color('#000');
                });

                if ($withDescription) {
                    $img->text(wordwrap($withDescription, 35, PHP_EOL), 75, $y + 60, function ($font) {
                        $font->file(public_path('fonts/Lexend-SemiBold.ttf'));
                        $font->size(30);
                        $font->color('#000');
                    });
                }

                $points = [
                    1200,  200,
                    1200,  630,
                    825,  630,
                ];

                $img->polygon($points, function ($draw) use ($tailwind) {
                    $draw->background($tailwind->shades['brand-100']);
                });

                $img->save($pathToImage);

                return asset('storage/og-' . $this->slug . '-' . $this->id . '.jpg?v=' . $this->updated_at->timestamp);
            } catch (\Throwable $exception) {
                return asset('images/og-template.jpg');
            }
        }

        return asset('storage/og-' . $this->slug . '-' . $this->id . '.jpg?v=' . $this->updated_at->timestamp);
    }
}
