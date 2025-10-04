<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Settings\WidgetSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    public function javascript(): Response
    {
        return response()
            ->view('widget.index')
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
        $userId = null;
        if ($request->filled('email')) {
            $user = User::firstOrCreate(
                ['email' => $request->input('email')],
                [
                    'name' => $request->input('name', 'Widget User'),
                    'password' => bcrypt(str()->random(32)),
                ]
            );
            $userId = $user->id;
        }

        // Create item
        $item = Item::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $userId,
            'private' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'item_id' => $item->id,
            'item_url' => route('items.show', $item),
        ], 201);
    }
}
