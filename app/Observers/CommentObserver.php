<?php

namespace App\Observers;

use App\Models\Comment;
use Xetaio\Mentions\Parser\MentionParser;

class CommentObserver
{
    public function created(Comment $comment)
    {
        $parser = new MentionParser($comment, [
            'regex_replacement' => [
                '{character}'  => '@',
                '{pattern}'  => '[A-Za-z0-9_-]',
                '{rules}'  => '{4,20}'
            ],
        ]);

        $content = $parser->parse($comment->content);

        $comment->updateQuietly([
            'content' => $content,
        ]);
    }
}
