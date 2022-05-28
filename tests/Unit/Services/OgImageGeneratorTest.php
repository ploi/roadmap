<?php

use App\Services\OgImageGenerator;
use Illuminate\Support\Facades\Storage;

it('can generate a simple og image', function () {
    $filename = md5(time()) . '.jpg';

    $generated = OgImageGenerator::make('Hi!')
                                 ->withSubject('A subject')
                                 ->withFilename($filename)
                                 ->generate();

    expect($generated->exists())->toBeTrue();

    Storage::disk('public')->assertExists("og-{$filename}")->delete("og-{$filename}");
});
