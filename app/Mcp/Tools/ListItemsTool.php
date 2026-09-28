<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('list-items')]
#[Description('List or search roadmap items (feature requests, ideas, bugs). Optionally filter on a project and board, and sort by newest, most votes or latest comment.')]
#[IsReadOnly]
#[IsIdempotent]
class ListItemsTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'project' => ['nullable', 'required_with:board'],
            'board' => ['nullable'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['latest', 'popular', 'last_commented'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'project.required_with' => 'A board can only be used together with a project.',
        ]);

        $query = Item::query()
            ->visibleForCurrentUser()
            ->with(['project', 'board', 'tags']);

        if (filled($validated['project'] ?? null)) {
            $project = $this->findProject($validated['project']);

            if (! $project) {
                return Response::error('Project not found.');
            }

            $query->where('items.project_id', $project->id);

            if (filled($validated['board'] ?? null)) {
                $board = $this->findBoard($project, $validated['board']);

                if (! $board) {
                    return Response::error('Board not found in this project.');
                }

                $query->where('items.board_id', $board->id);
            }
        }

        if (filled($validated['search'] ?? null)) {
            $query->where(fn ($query) => $query
                ->where('items.title', 'like', '%' . $validated['search'] . '%')
                ->orWhere('items.content', 'like', '%' . $validated['search'] . '%'));
        }

        match ($validated['sort'] ?? 'latest') {
            'popular' => $query->orderByDesc('items.total_votes'),
            'last_commented' => $query->withMax('comments', 'created_at')->orderByDesc('comments_max_created_at'),
            default => $query->latest('items.created_at'),
        };

        $items = $query->orderByDesc('items.id')->paginate(
            perPage: $validated['per_page'] ?? 20,
            page: $validated['page'] ?? 1,
        );

        return Response::json([
            'items' => collect($items->items())->map(fn (Item $item) => $this->presentItem($item))->all(),
            'page' => $items->currentPage(),
            'per_page' => $items->perPage(),
            'total' => $items->total(),
            'has_more_pages' => $items->hasMorePages(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'project' => $schema->string()->description('Only list items of this project (ID or slug).'),
            'board' => $schema->string()->description('Only list items on this board (ID or slug), requires a project.'),
            'search' => $schema->string()->description('Search the title and content of items.'),
            'sort' => $schema->string()->enum(['latest', 'popular', 'last_commented'])->description('Sort order, defaults to latest.'),
            'per_page' => $schema->integer()->min(1)->max(50)->description('Number of items per page, defaults to 20.'),
            'page' => $schema->integer()->min(1)->description('The page to fetch, defaults to 1.'),
        ];
    }
}
