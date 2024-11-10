<?php

namespace App\Rules;

use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\Rule;

class ProfanityCheck implements Rule
{
    /**
     * @param $attribute
     * @param string $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $words = explode(' ', $value);

        foreach ($words as $word) {
            if (in_array($word, app(GeneralSettings::class)->profanity_words)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'The content here contains profanity words, please correct these.';
    }
}
