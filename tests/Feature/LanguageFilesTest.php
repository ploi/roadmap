<?php

test('every language file returns an array without printing output', function (string $file) {
    ob_start();
    $translations = require $file;
    $output = ob_get_clean();

    expect($output)->toBe('')
        ->and($translations)->toBeArray();
})->with(fn () => collect(glob(dirname(__DIR__, 2).'/lang/*/*.php'))
    ->mapWithKeys(fn (string $file) => [basename(dirname($file)).'/'.basename($file) => $file])
    ->all());
