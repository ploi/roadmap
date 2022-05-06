<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BoardsController;
use App\Http\Controllers\ProjectController;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('my', MyController::class)->middleware('auth')->name('my');
Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('projects/{project}/items/{item}', [ItemController::class, 'show'])->name('projects.items.show');
Route::post('projects/{project}/items/{item}/vote', [ItemController::class, 'vote'])->middleware('auth')->name('projects.items.vote');
Route::get('projects/{project}/boards/{board}', [BoardsController::class, 'show'])->name('projects.boards.show');
