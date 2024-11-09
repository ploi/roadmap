<?php

namespace App\Traits;

use App\Services\OgImageGenerator;

trait HasOgImage
{
    public function getOgImage(?string $description, string $subject = 'Roadmap'): string
    {
        return OgImageGenerator::make($this->title)
                               ->withSubject($subject)
                               ->withDescription($description)
                               ->withPolygonDecoration()
                               ->withFilename("{$this->slug}-{$this->id}.jpg")
                               ->generate()
                               ->getPublicUrl();
    }
}
