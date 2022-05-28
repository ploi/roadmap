<?php

namespace App\Traits;

use App\Services\OgImageGenerator;

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
