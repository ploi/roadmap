<?php

namespace App\Http\Livewire\Modals\Item\Comment;

use App\Models\Comment;
use LivewireUI\Modal\ModalComponent;

class ShowCommentActivitiesModal extends ModalComponent
{
    public $comment;

    public function mount(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function render()
    {
        return view('livewire.modals.items.comments.show-activitylog');
    }
}
