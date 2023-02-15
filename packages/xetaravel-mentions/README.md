> <h1 align="center">Xetaravel Mentions</h1>
>
> |Unit Tests|Coverage|Stable Version|Downloads|Laravel|License|
> |:-------:|:------:|:-------:|:------:|:-------:|:-------:|
> |[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/XetaIO/Xetaravel-Mentions/Unit%20Tests?style=flat-square)](https://github.com/XetaIO/Xetaravel-Mentions/actions/workflows/tests.yml)|[![Coverage Status](https://img.shields.io/codecov/c/github/XetaIO/Xetaravel-Mentions?style=flat-square)](https://app.codecov.io/gh/XetaIO/Xetaravel-Mentions)|[![Latest Stable Version](https://img.shields.io/packagist/v/XetaIO/Xetaravel-Mentions.svg?style=flat-square)](https://packagist.org/packages/xetaio/xetaravel-mentions)|[![Total Downloads](https://img.shields.io/packagist/dt/xetaio/xetaravel-mentions.svg?style=flat-square)](https://packagist.org/packages/xetaio/xetaravel-mentions)|[![Laravel 8.0](https://img.shields.io/badge/Laravel->=8.0-f4645f.svg?style=flat-square)](http://laravel.com)|[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/XetaIO/Xetaravel-Mentions/blob/master/LICENSE)|
>
> A package to parse `@mentions` from a text and mention the user with a notification.
>
> **By default** this package is configured to parse any text type and it will replace any matched `@mention` with a markdown link (`[@Mention](/users/profile/@Mention)`) if the mentionned user exist in the database. It will also send a notification with the Laravel `Notifiable` trait to all mentionned users. (Inspired from the [laravel-mentions](https://github.com/jameslkingsley/laravel-mentions) package.)
>
> Quick example :
>
> **Input** text :
> ```md
>   Lorem ipsu @admin crepu @Member quis nostrud @UserDoesNotExist ullamcorper fail@mention nostrud @admin.
> ```
> **Output** text :
> ```md
> Lorem ipsu [@Admin](/users/profile/@Admin) crepu [@Member](/users/profile/@Member) quis nostrud
> @UserDoesNotExist ullamcorper fail@mention nostrud [@Admin](/users/profile/@Admin).
> ```
> **And** Both `Admin` and `Member` users will be notified. **But** `Admin` will be notified only **one** time. (Yes the Parser include an anti-spam rule.)
>
> ## Table of Contents
> * [Requirement](#requirement)
> * [Installation](#installation)
>     * [ServiceProviders](#serviceproviders)
>     * [Vendor Publish](#vendor-publish)
>     * [Configuration](#configuration)
> * [Usage](#usage)
>     * [Parser configuration](#parser-configuration)
>     * [Parser configuration methods](#parser-configuration-methods)
> * [Custom Parser](#custom-parser)
> * [Notification](#notification)
> * [Contribute](#contribute)
>
> ## Requirement
> ![PHP](https://img.shields.io/badge/PHP->=7.3-brightgreen.svg?style=flat-square)
>
> ## Installation
>
> ```
> composer require xetaio/xetaravel-mentions
> ```
>
> #### ServiceProviders
> Import the `MentionServiceProvider` in your `config/app.php`:
> ```php
> 'providers' => [
>   //...
>   Xetaio\Mentions\Providers\MentionServiceProvider::class,
>   //...
> ]
> ```
>
> #### Vendor Publish
> Publish the vendor files to your application (included the config file `config/mentions.php` and the migration file) :
> ```php
> php artisan vendor:publish --provider="Xetaio\Mentions\Providers\MentionServiceProvider"
> ```
> Then migrate the database :
> ```sh
> php artisan migrate
> ```
>
> #### Configuration
> ```php
> <?php
>
> return [
>     'pools' => [
>         // Here you configure as many pools as you want. But basically we
>         // notify only the Users.
>         'users' => [
>             // Model that will be mentioned.
>             'model' => App\Models\User::class,
>
>             // The column that will be used to search the model by the parser.
>             'column' => 'username',
>
>             // The route used to generate the user link.
>             'route' => '/users/profile/@',
>
>             // Notification class to use when this model is mentioned.
>             'notification' => App\Notifications\MentionNotification::class,
>         ]
>     ]
> ];
> ```
>
> ## Usage
> First, you will need to add the `HasMentionTrait` to the *mentioner* Model :
> ```php
> <?php
> namespace App\Models;
>
> use Illuminate\Database\Eloquent\Model;
> use Xetaio\Mentions\Models\Traits\HasMentionsTrait;
>
> class Comment extends Model
> {
>     use HasMentionsTrait;
> }
> ```
> Now, you can start to parse a text in your controller or where you want :
> ```php
> <?php
> namespace App\Http\Controllers;
>
> use Xetaio\Mentions\Parser\MentionParser;
>
> class CommentController extends Controller
> {
>      public function create(Request $request)
>      {
>          // Handle the comment creation however you like
>          $comment = Comment::create($request->all());
>
>          // Register a new Parser and parse the content.
>          $parser = new MentionParser($comment);
>          $content = $parser->parse($comment->content);
>
>          /**
>           * Re-assign the parsed content and save it.
>           *
>           * Note : If you use a custom Parser and you don't modify
>           * the `$content` in your custom Parser, you can ignore that.
>           */
>          $comment->content = $content;
>          $comment->save();
>      }
> }
> ```
> And that's all ! At least with the default configuration. :stuck_out_tongue_closed_eyes:
>
> #### Parser configuration
> The `MentionParser` take a second parameter who is a configuration array, **this is the default configuration** :
> ```php
> <?php
>
> [
>      // The pool used with the parser.
>      'pool' => 'users',
>
>      // If `false`, the parser won't mention the user.
>      'mention' => true,
>
>      /**
>       * If `false` the parser won't notify the user.
>       *
>       * Note : If the `mention` option is set to `false`, this setting is ignored.
>       */
>      'notify' => true,
>
>      /**
>       * The character that will trigger the mention.
>       * Note : If you modify this setting, you will also need to modify
>       * the `regex_replacement.{character}` option to match this setting.
>       */
>      'character' => '@',
>
>      // The regex used to parse the mentions.
>      'regex' => '/\s({character}{pattern}{rules})/',
>
>      /**
>       * The replacement used to replace the regex pattarn.
>       * Can be usefull if you want to allow a special character in the mention like `_` or `-`
>       * or pass dynamic value to the regex.
>       *
>       * Note : The parse use the PHP function `strtr` to replace the pattarn in the regex.
>       */
>      'regex_replacement' => [
>          '{character}'  => '@',
>          '{pattern}'  => '[A-Za-z0-9]',
>          '{rules}'  => '{4,20}'
>     ]
> ]
> ```
> The configuration is merged with the default configuration, so you can set only the options that you want to modify. Exemple :
> ```php
> <?php
>
> $parser = new MentionParser($comment, [
>     'pool' => 'members',
>     'notify' => false
> ]);
> ```
> You can also set a configuration at the runtime :
> ```php
> <?php
>
> $parser = new MentionParser($comment);
> $parser->setOption('notify', false);
> $content = $parser->parse($comment->content);
> ```
> Or even get a configuration option value :
> ```php
> <?php
>
> $value = $parser->getOption('notify');
> // false
> ```
> #### Parser configuration methods :
> The parser use the [Xety/Configurator](https://github.com/Xety/Configurator) package to manage the configuration.
> Check this repository to get all the methods and their description.
>
> |Function Name|Description|
> | --- | --- |
> |`setConfig(array $config)`|Sets a configuration array.(Will replace all configuration options **be carefull**)|
> |`getConfig()`|Get all the configuration options.|
> |`setOption(string $name, $value)`|Set a value for the given key.|
> |`getOption(string $name)`|Get a configuration value.|
> |`hasOption(string $name)`|Determines if current instance has the given option.|
> |`mergeConfig(array $values, bool $invert = false)`|Merges configuration values with the new ones. If the `$invert` param is set to `true` it will merge the default configuration **into** the `$values`.|
>
>
> ## Custom Parser
> If you want more flexibility for the Parser, the best way is to create a new Parser and overwrite the methods that you want to modify. For an example, let's create a new Parser that will return a HTML link instead of a Markdown link :
> ```php
> <?php
> namespace App\Parser;
>
> use Illuminate\Support\Str;
> use Xetaio\Mentions\Parser\MentionParser;
>
> class CustomParser extends MentionParser
> {
>
>     protected function replace(array $match): string
>     {
>         $character = $this->getOption('character');
>         $mention = Str::title(str_replace($character, '', trim($match[0])));
>
>         $route = config('mentions.pools.' . $this->getOption('pool') . '.route');
>
>         $link = $route . $mention;
>
>         // Here we return a HTML link instead of the default Markdown.
>         return " <a class=\"link\" href=\"{$link} \">{$character}{$mention}</a>";
>     }
> }
> ```
> To use it :
> ```php
> <?php
>
> $parser = new \App\Parser\CustomParser($comment);
> $content = $parser->parse($comment->content);
> ```
> You can of course overwrite all Parser's methods if you need to.
>
> ## Notification
> You will need to write your own Notififation class, but I'm cool with you, you can find an example here using the delivery channel `database` :
> ```php
> <?php
> namespace App\Notifications;
>
> use Illuminate\Bus\Queueable;
> use Illuminate\Notifications\Notification;
>
> class MentionNotification extends Notification
> {
>     use Queueable;
>
>     /**
>      * The Comment instance.
>      *
>      * @var \Illuminate\Database\Eloquent\Model
>      */
>     public $model;
>
>     /**
>      * Create a new notification instance.
>      *
>      * @param \Illuminate\Database\Eloquent\Model $model
>      */
>     public function __construct($model)
>     {
>         $this->model = $model;
>     }
>
>     /**
>      * Get the notification's delivery channels.
>      *
>      * @param mixed $notifiable
>      *
>      * @return array
>      */
>     public function via($notifiable): array
>     {
>         return ['database'];
>     }
>
>     /**
>      * Get the array representation of the notification.
>      *
>      * @param mixed $notifiable
>      *
>      * @return array
>      */
>     public function toDatabase($notifiable): array
>     {
>         // The instance `$this->model` represent the `Comment` model.
>         $username = $this->model->user->username;
>         $modelId = $this->model->getKey();
>
>         $message = "<strong>@{ $username }</strong> has mentionned your name in his comment !";
>
>         // You could (and probably should) use a route name here with the function `route()`.
>         $link = "/comment/show/{ $modelId }";
>
>         return [
>             'message' => $message,
>             'link' => $link,
>             'type' => 'mention'
>         ];
>     }
> }
> ```
>
> ## Contribute
> If you want to contribute, please [follow this guide](https://github.com/XetaIO/Xetaravel-Mentions/blob/master/.github/CONTRIBUTING.md).
