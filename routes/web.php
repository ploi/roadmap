<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
Route::get('projects/{project}/items/{item}', [\App\Http\Controllers\ItemController::class, 'show'])->name('projects.items.show');

Route::get('projects/{project}/boards/{board}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('boards.show');

