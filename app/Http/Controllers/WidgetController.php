<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Settings\WidgetSettings;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;
use App\Settings\ActivityWidgetSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    public function javascript(): Response
    {
        return response()
            ->view('widgets.feedback.index')
            ->header('Content-Type', 'application/javascript');
    }

    public function config(Request $request): JsonResponse
    {
        $settings = app(WidgetSettings::class);

        if (!$settings->enabled) {
            return response()->json(['enabled' => false], 200);
        }

        // Validate origin domain if restrictions are set
        if (!empty($settings->allowed_domains)) {
            $origin = $request->header('Origin') ?? $request->header('Referer');

            if ($origin) {
                $domain = parse_url($origin, PHP_URL_HOST);
                $allowed = false;

                foreach ($settings->allowed_domains as $allowedDomain) {
                    if ($domain === $allowedDomain || str_ends_with($domain, '.' . $allowedDomain)) {
                        $allowed = true;
                        break;
                    }
                }

                if (!$allowed) {
                    return response()->json(['enabled' => false], 200);
                }
            }
        }

        return response()->json([
            'enabled' => $settings->enabled,
            'position' => $settings->position,
            'primary_color' => $settings->primary_color,
            'button_text' => $settings->button_text,
            'hide_button' => $settings->hide_button,
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $settings = app(WidgetSettings::class);

        // Check if widget is enabled
        if (!$settings->enabled) {
            return response()->json(['error' => 'Widget is not enabled'], 403);
        }

        // Validate origin domain if restrictions are set
        if (!empty($settings->allowed_domains)) {
            $origin = $request->header('Origin') ?? $request->header('Referer');

            if ($origin) {
                $domain = parse_url($origin, PHP_URL_HOST);
                $allowed = false;

                foreach ($settings->allowed_domains as $allowedDomain) {
                    if ($domain === $allowedDomain || str_ends_with($domain, '.' . $allowedDomain)) {
                        $allowed = true;
                        break;
                    }
                }

                if (!$allowed) {
                    return response()->json(['error' => 'Domain not allowed'], 403);
                }
            }
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Only create/find user if email is provided
        $user = null;
        if ($request->filled('email')) {
            $user = User::firstOrCreate(
                ['email' => $request->input('email')],
                [
                    'name' => $request->input('name', 'Widget User'),
                    'password' => bcrypt(str()->random(32)),
                ]
            );
        }

        // Temporarily set the user for this request without triggering login events
        if ($user) {
            auth()->setUser($user);
        }

        // Create item
        $item = Item::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $user?->id,
            'private' => false,
        ]);

        // Automatically upvote the item for the user
        if ($user) {
            $item->toggleUpvote($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'item_id' => $item->id,
            'item_url' => route('items.show', $item),
        ], 201);
    }

    public function activityJavascript(): Response
    {
        return response()
            ->view('widgets.activity.index')
            ->header('Content-Type', 'application/javascript');
    }

    public function activityConfig(Request $request): JsonResponse
    {
        $settings = app(ActivityWidgetSettings::class);

        if (!$settings->enabled) {
            return response()->json(['enabled' => false], 200);
        }

        // Validate origin domain if restrictions are set
        if (!empty($settings->allowed_domains)) {
            $origin = $request->header('Origin') ?? $request->header('Referer');

            if ($origin) {
                $domain = parse_url($origin, PHP_URL_HOST);
                $allowed = false;

                foreach ($settings->allowed_domains as $allowedDomain) {
                    if ($domain === $allowedDomain || str_ends_with($domain, '.' . $allowedDomain)) {
                        $allowed = true;
                        break;
                    }
                }

                if (!$allowed) {
                    return response()->json(['enabled' => false], 200);
                }
            }
        }

        return response()->json([
            'enabled' => $settings->enabled,
            'position' => $settings->position,
            'primary_color' => $settings->primary_color,
            'button_text' => $settings->button_text,
            'hide_button' => $settings->hide_button,
            'modal_title' => $settings->modal_title,
            'items_limit' => $settings->items_limit,
        ]);
    }

    public function activityList(Request $request): JsonResponse
    {
        $settings = app(ActivityWidgetSettings::class);

        // Check if widget is enabled
        if (!$settings->enabled) {
            return response()->json(['error' => 'Widget is not enabled'], 403);
        }

        // Validate origin domain if restrictions are set
        if (!empty($settings->allowed_domains)) {
            $origin = $request->header('Origin') ?? $request->header('Referer');

            if ($origin) {
                $domain = parse_url($origin, PHP_URL_HOST);
                $allowed = false;

                foreach ($settings->allowed_domains as $allowedDomain) {
                    if ($domain === $allowedDomain || str_ends_with($domain, '.' . $allowedDomain)) {
                        $allowed = true;
                        break;
                    }
                }

                if (!$allowed) {
                    return response()->json(['error' => 'Domain not allowed'], 403);
                }
            }
        }

        $page = max(1, (int) $request->input('page', 1));
        $search = $request->input('search', '');

        $paginator = Activity::query()
            ->with(['causer', 'subject.user', 'subject.comments'])
            ->where(function (Builder $query) use ($search) {
                $query->whereHasMorph('subject', ['App\Models\Item'], function (Builder $query) use ($search) {
                    $query->where('private', false);

                    // Add search filter for items
                    if (!empty($search)) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                })
                ->orWhereHasMorph('subject', ['App\Models\Comment'], function (Builder $query) use ($search) {
                    $query->where('private', false);

                    // For comments, search the related item title
                    if (!empty($search)) {
                        $query->whereHas('item', function (Builder $itemQuery) use ($search) {
                            $itemQuery->where('title', 'like', '%' . $search . '%');
                        });
                    }
                });
            })
            ->whereNotNull('causer_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $page);

        $activities = $paginator->map(function ($activity) {
            $subject = $activity->subject;
            $description = ucfirst($activity->description);

            if ($subject instanceof \App\Models\Item) {
                $title = str($subject->title)->limit(50);
                $description = ucfirst("{$activity->description}: \"{$title}\"");
                $url = $subject->board && $subject->project
                    ? route('projects.items.show', [$subject->project, $subject])
                    : route('items.show', $subject);
            } elseif ($subject instanceof \App\Models\Comment && $subject->item) {
                $title = str($subject->item->title)->limit(50);
                $description = ucfirst("commented on \"{$title}\"");
                $url = route('items.show', $subject->item) . "#comment-{$subject->id}";
            } else {
                $url = null;
            }

            return [
                'user' => $activity->causer?->name ?? 'Unknown',
                'description' => $description,
                'votes' => $subject instanceof \App\Models\Item ? ($subject->total_votes ?? 0) : null,
                'comments' => $subject instanceof \App\Models\Item ? $subject->comments->count() : null,
                'created_at' => $activity->created_at->toIso8601String(),
                'url' => $url,
            ];
        })->filter(fn ($activity) => $activity['url'] !== null)->values();

        return response()->json([
            'activities' => $activities,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }
}
