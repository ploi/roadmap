<?php

namespace App\Rules;

use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\Rule;

class ProfanityCheck implements Rule
{
    public function passes($attribute, $value)
    {
        if (! is_string($value)) {
            return true;
        }

        foreach (app(GeneralSettings::class)->profanity_words as $profanityWord) {
            $terms = preg_split('/\s+/u', trim((string) $profanityWord), -1, PREG_SPLIT_NO_EMPTY);

            if (empty($terms)) {
                continue;
            }

            $phrase = implode('\s+', array_map(fn (string $term) => preg_quote($term, '/'), $terms));

            if (preg_match('/(?<![\p{L}\p{N}])' . $phrase . '(?![\p{L}\p{N}])/iu', $value) === 1) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'The content here contains profanity words, please correct these.';
    }
}
