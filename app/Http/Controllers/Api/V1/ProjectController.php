<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Api\V1\ProjectResource;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'updated_since' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return ProjectResource::collection(
            Project::query()
                ->when($request->date('updated_since')?->setTimezone(config('app.timezone')), fn ($query, $since) => $query->where('updated_at', '>=', $since))
                ->orderBy('id')
                ->paginate($request->integer('per_page') ?: 50)
                ->withQueryString()
        );
    }
}
