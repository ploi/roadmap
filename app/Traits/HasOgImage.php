<?php

namespace App\Traits;

use App\Services\OgImageGenerator;
use App\Services\Tailwind;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait HasOgImage
{
    public function getOgImage($withDescription = false, $subject = 'Roadmap')
    {
        $service = (new OgImageGenerator())
            ->setSubject($subject)
            ->setTitle($this->title)
            ->setDescription($withDescription)
            ->polygon()
            ->setImageName($this->slug . '-' . $this->id . '.jpg');

        return $service->generateImage();
    }
}
