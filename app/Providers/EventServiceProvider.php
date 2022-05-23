<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Vote;
use App\Models\Project;
use App\Observers\CommentObserver;
use App\Observers\ItemObserver;
use App\Observers\VoteObserver;
use App\Observers\ProjectObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $observers = [
        Vote::class => [VoteObserver::class],
        Project::class => [ProjectObserver::class],
        Item::class => [ItemObserver::class],
        Comment::class => [CommentObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
