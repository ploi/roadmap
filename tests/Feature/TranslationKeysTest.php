<?php

use Illuminate\Support\Facades\Lang;
use Symfony\Component\Finder\Finder;

test('all translation keys used in the codebase exist', function () {
    $groups = collect(glob(lang_path('en/*.php')))
        ->map(fn (string $file) => basename($file, '.php'))
        ->all();

    $files = Finder::create()
        ->files()
        ->name('*.php')
        ->in([app_path(), resource_path('views')]);

    $missing = [];

    foreach ($files as $file) {
        // Only match keys passed as a complete string literal, so concatenated keys are skipped.
        preg_match_all(
            '/(?:\b__|\btrans|\btrans_choice|@lang)\(\s*([\'"])([a-z0-9_-]+\.[^\'"$]+)\1\s*[,)]/i',
            $file->getContents(),
            $matches
        );

        foreach ($matches[2] as $key) {
            if (in_array(strtok($key, '.'), $groups, true) && ! Lang::has($key, 'en', false)) {
                $missing[] = $file->getRelativePathname().': '.$key;
            }
        }
    }

    expect($missing)->toBeEmpty();
});
