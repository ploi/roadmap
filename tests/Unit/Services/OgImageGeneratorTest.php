<?php

use App\Services\OgImageGenerator;
use Illuminate\Support\Facades\Storage;

it('can generate a simple og image', function () {
    $fileName = md5(time()) . '.jpg';

    $service = new OgImageGenerator();

    $service
        ->setTitle('Hi!')
        ->setSubject('A subject')
        ->setImageName($fileName)
        ->generateImage();

    Storage::disk('public')->assertExists('og-' . $fileName)->delete('og-' . $fileName);
});
