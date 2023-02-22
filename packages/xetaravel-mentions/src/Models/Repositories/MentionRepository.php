<?php
namespace Xetaio\Mentions\Models\Repositories;

use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Models\Mention;

class MentionRepository
{

    /**
     * Gets all mentions for this model.
     *
     * @return any
     */
    public static function get(Model $model)
    {
        return Mention::where('model_type', get_class($model))
            ->where('model_id', $model->getKey())
            ->get();
    }

    /**
     * Creates a new mention.
     *
     * @return \Xetaio\Mentions\Models\Mention
     */
    public static function create(Model $model, Model $recipient, $notify = true): Mention
    {
        $mention = Mention::firstOrCreate([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'recipient_type' => get_class($recipient),
            'recipient_id' => $recipient->getKey()
        ]);

        if ($notify) {
            $mention->notify($model, $recipient);
        }

        return $mention;
    }
}
