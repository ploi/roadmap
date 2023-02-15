<?php
namespace Xetaio\Mentions\Parser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Xety\Configurator\Configurator;

class MentionParser extends Configurator
{
    /**
     * The model used to mention the user.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The default configuration used by the parser.
     *
     * @var array
     */
    protected $defaultConfig = [
        'pool' => 'users',
        'mention' => true,
        'notify' => true,
        'character' => '@',
        'regex' => '/({character}{pattern}{rules})/',
        'regex_replacement' => [
            '{character}' => '@',
            '{pattern}' => '[A-Za-z0-9]',
            '{rules}' => '{4,20}'
        ]
    ];

    /**
     * Constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model used to mention the user.
     * @param array $config The config to merge with the default config.
     */
    public function __construct(Model $model, array $config = [])
    {
        $this->setConfig($this->defaultConfig);
        $this->model = $model;

        $this->mergeConfig($config);
    }

    /**
     * Parse a text and determine if it contains mentions. If it does,
     * then we transform the mentions to a markdown link and we notify the user.
     *
     * @param null|string $input The string to parse.
     *
     * @return null|string
     */
    public function parse($input)
    {
        if (is_null($input) || empty($input)) {
            return $input;
        }
        $character = $this->getOption('character');
        $regex = strtr($this->getOption('regex'), $this->getOption('regex_replacement'));

        preg_match_all($regex, $input, $matches);

        $matches = array_map([$this, 'mapper'], $matches[0]);

        $matches = $this->removeNullKeys($matches);
        $matches = $this->prepareArray($matches);

        $output = preg_replace_callback($matches, [$this, 'replace'], $input);

        return $output;
    }

    /**
     * Replace the mention with a markdown link.
     *
     * @param array $match The mention to replace.
     *
     * @return string
     */
    protected function replace(array $match): string
    {
        $character = $this->getOption('character');
        $mention = Str::title(str_replace($character, '', trim($match[0])));

        $route = config('mentions.pools.' . $this->getOption('pool') . '.route');

        $link = $route . $mention;

        return "[{$character}{$mention}]($link)";
    }

    /**
     * Prepare the array before calling the replace function.
     *
     * We basically order the array in alphabetic order, then we reverse it
     * so it will match the largest name first, else it can remove
     * `@admin2` if it match `@admin` first (based on the default regex).
     *
     * @param array $array The array to prepare
     *
     * @return array
     */
    protected function prepareArray(array $array): array
    {
        sort($array, SORT_STRING);

        $array = array_reverse($array);

        return $array;
    }

    /**
     * Remove all `null` key in the given array.
     *
     * @param array $array The array where the filter should be applied.
     *
     * @return array
     */
    protected function removeNullKeys(array $array): array
    {
        return array_filter($array, function ($key) {
            return ($key !== null);
        });
    }

    /**
     * Handle a mention and return it has a regex. If you want to delete
     * this mention from the out array, just return `null`.
     *
     * @param string $key The mention that has been matched.
     *
     * @return null|string
     */
    protected function mapper(string $key)
    {
        $character = $this->getOption('character');
        $config = config('mentions.pools.' . $this->getOption('pool'));

        $mention = str_replace($character, '', trim($key));

        $mentionned = $config['model']::whereRaw("LOWER({$config['column']}) = ?", [Str::lower($mention)])->first();

        if ($mentionned == false) {
            return null;
        }

        if ($this->getOption('mention') == true && $mentionned->getKey() !== Auth::id()) {
            $this->model->mention($mentionned, $this->getOption('notify'));
        }

        return '/' . preg_quote($key) . '(?!\w)/';
    }
}
