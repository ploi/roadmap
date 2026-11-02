<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Widget routes (public)
Route::prefix('widget')->group(function () {
    Route::get('/config', [WidgetController::class, 'config']);
    Route::post('/submit', [WidgetController::class, 'submit']);
});

// Activity Widget routes (public)
Route::prefix('activity-widget')->group(function () {
    Route::get('/config', [WidgetController::class, 'activityConfig']);
    Route::get('/activities', [WidgetController::class, 'activityList']);
});

// Read API (admin and employee tokens)
Route::prefix('v1')->middleware(['auth:sanctum', 'api.access'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/comments', [CommentController::class, 'index']);
});
