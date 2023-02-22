<?php
namespace Tests\vendor\Parser;

use Illuminate\Support\Str;
use Xetaio\Mentions\Parser\MentionParser;

class CustomParser extends MentionParser
{

    protected function replace(array $match): string
    {
        $character = $this->getOption('character');
        $mention = Str::title(str_replace($character, '', trim($match[0])));

        $route = '/users/show/@';

        $link = $route . $mention;

        return "<a class=\"link\" href=\"{$link}\">{$character}{$mention}</a>";
    }
}
