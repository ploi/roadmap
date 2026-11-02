<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'updated_since' => ['nullable', 'date'],
            'item_id' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return CommentResource::collection(
            Comment::query()
                ->when($request->date('updated_since'), fn ($query, $since) => $query->where('updated_at', '>=', $since))
                ->when($request->integer('item_id'), fn ($query, $itemId) => $query->where('item_id', $itemId))
                ->orderBy('id')
                ->paginate($request->integer('per_page', 50))
                ->withQueryString()
        );
    }
}
