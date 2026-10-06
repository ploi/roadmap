<?php

use App\Rules\ProfanityCheck;
use App\Settings\GeneralSettings;

beforeEach(function () {
    GeneralSettings::fake([
        'profanity_words' => ['fuck', 'asshole', 'dick', 'screw you'],
    ]);
});

test('content containing a profanity word fails', function (string $content) {
    expect((new ProfanityCheck())->passes('content', $content))->toBeFalse();
})->with([
    'exact word' => 'fuck',
    'different casing' => 'Fuck this feature',
    'trailing punctuation' => 'What the fuck!',
    'surrounded by punctuation' => '(asshole)',
    'separated by a newline' => "Nice idea\ndick",
    'multi word entry' => 'Screw you',
    'multi word entry across whitespace' => "screw\n  you, really",
]);

test('content without a profanity word passes', function (string $content) {
    expect((new ProfanityCheck())->passes('content', $content))->toBeTrue();
})->with([
    'clean sentence' => 'Please add dark mode',
    'word containing a profanity word' => 'I love reading Dickens',
    'multi word entry split by other words' => 'Screw the lid on, thank you',
]);

test('empty profanity entries are ignored', function () {
    GeneralSettings::fake(['profanity_words' => ['', '  ']]);

    expect((new ProfanityCheck())->passes('content', 'Please add dark mode'))->toBeTrue();
});
