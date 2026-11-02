<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Item */
class ItemResource extends JsonResource
{
    /**
     * The board is the status of an item.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'project_id' => $this->project_id,
            'board_id' => $this->board_id,
            'status' => $this->board?->title,
            'user_id' => $this->user_id,
            'private' => (bool) $this->private,
            'pinned' => (bool) $this->pinned,
            'total_votes' => (int) $this->total_votes,
            'issue_number' => $this->issue_number,
            'url' => $this->view_url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
