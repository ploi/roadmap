<?php
namespace Xetaio\Mentions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Parser\Exceptions\CannotFindPoolException;

class Mention extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'recipient_type',
        'recipient_id'
    ];

    /**
     * The default notification class.
     *
     * @var string
     */
    protected $mentionNotification = \App\Notifications\MentionNotification::class;

    /**
     * Gets the recipient model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recipient(): Model
    {
        return $this->recipient_type::findOrFail($this->recipient_id);
    }

    /**
     * Notify the mentioned model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Xetaio\Mentions\Models\Mention
     */
    public function notify(Model $model, Model $recipient): Mention
    {
        $pool = $this->pool($recipient);

        $notificationClass = class_exists($pool->notification) ? $pool->notification : $this->mentionNotification;

        if (class_exists($notificationClass)) {
            $recipient->notify(new $notificationClass($model));
        }

        return $this;
    }

    /**
     * Gets the pool config for the given model.
     *
     * @return void
     */
    public function pool(Model $model)
    {
        $name = get_class($model);

        foreach (config('mentions.pools') as $key => $pool) {
            if ($pool['model'] == $name) {
                $result = (object)$pool;
                $result->key = $key;

                return $result;
            }
        }

        throw new CannotFindPoolException("Cannot find the mention pool for {$name}");
    }
}
