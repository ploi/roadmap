<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ItemResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'updated_since' => ['nullable', 'date'],
            'project_id' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return ItemResource::collection(
            Item::query()
                ->with('board')
                ->when($request->date('updated_since'), fn ($query, $since) => $query->where('updated_at', '>=', $since))
                ->when($request->integer('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
                ->orderBy('id')
                ->paginate($request->integer('per_page', 50))
                ->withQueryString()
        );
    }
}
