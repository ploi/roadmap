<?php

namespace App\Observers;

use Throwable;
use App\Models\Board;
use App\Models\Project;
use App\Enums\WebhookEvent;
use App\Services\WebhookDispatcher;
use App\Http\Resources\Api\V1\ProjectResource;
use Illuminate\Support\Facades\Storage;

class ProjectObserver
{
    public function updated(Project $project): void
    {
        $originalIconImage = $project->getOriginal('icon_image');

        if ($project->wasChanged('icon_image') && $originalIconImage) {
            Storage::disk('public')->delete($originalIconImage);
        }

        WebhookDispatcher::dispatch(WebhookEvent::ProjectUpdated, (new ProjectResource($project))->resolve());
    }

    public function deleting(Project $project)
    {
        if ($project->icon_image) {
            Storage::disk('public')->delete($project->icon_image);
        }

        try {
            Storage::delete('public/og-' . $project->slug . '-' . $project->id . '.jpg');
        } catch (Throwable $exception) {
        }

        $project->boards->each(fn (Board $board) => $board->delete());
    }
}
