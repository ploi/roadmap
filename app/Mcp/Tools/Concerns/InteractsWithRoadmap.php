<?php

namespace App\Mcp\Tools\Concerns;

use App\Models\Item;
use App\Models\User;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Project;

trait InteractsWithRoadmap
{
    /**
     * Find a project the current user is allowed to see by its ID or slug.
     */
    protected function findProject(int|string $identifier): ?Project
    {
        return Project::query()
            ->visibleForCurrentUser()
            ->where(fn ($query) => $this->whereIdentifier($query, 'projects', $identifier))
            ->first();
    }

    /**
     * Find an item the current user is allowed to see by its ID or slug.
     */
    protected function findItem(int|string $identifier): ?Item
    {
        return Item::query()
            ->visibleForCurrentUser()
            ->where(fn ($query) => $this->whereIdentifier($query, 'items', $identifier))
            ->first();
    }

    /**
     * Find a board of the given project by its ID or slug, hidden boards are only available to admins and employees.
     */
    protected function findBoard(Project $project, int|string $identifier): ?Board
    {
        return $project->boards()
            ->when(! $this->currentUser()?->hasAdminAccess(), fn ($query) => $query->visible())
            ->where(fn ($query) => $this->whereIdentifier($query, 'boards', $identifier))
            ->first();
    }

    protected function currentUser(): ?User
    {
        return auth()->user();
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->title,
            'group' => $project->group,
            'description' => $project->description,
            'private' => $project->private,
            'url' => route('projects.show', $project),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentBoard(Board $board): array
    {
        return [
            'id' => $board->id,
            'slug' => $board->slug,
            'title' => $board->title,
            'description' => $board->description,
            'visible' => $board->visible,
            'comments_blocked' => $board->block_comments,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentItem(Item $item): array
    {
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => $item->title,
            'excerpt' => $item->excerpt,
            'project' => $item->project ? ['id' => $item->project->id, 'slug' => $item->project->slug, 'title' => $item->project->title] : null,
            'board' => $item->board ? ['id' => $item->board->id, 'slug' => $item->board->slug, 'title' => $item->board->title] : null,
            'votes' => (int) $item->total_votes,
            'pinned' => $item->pinned,
            'private' => $item->private,
            'tags' => $item->tags->pluck('name')->values()->all(),
            'url' => $item->view_url,
            'created_at' => $item->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'parent_id' => $comment->parent_id,
            'author' => $comment->user?->name,
            'author_username' => $comment->user?->username,
            'content' => $comment->content,
            'private' => $comment->private,
            'votes' => (int) $comment->total_votes,
            'created_at' => $comment->created_at?->toIso8601String(),
        ];
    }

    /**
     * Match a model on its ID when the identifier is numeric, and always on its slug.
     */
    private function whereIdentifier($query, string $table, int|string $identifier): void
    {
        if (is_numeric($identifier)) {
            $query->where("{$table}.id", (int) $identifier);
        }

        $query->orWhere("{$table}.slug", (string) $identifier);
    }
}
