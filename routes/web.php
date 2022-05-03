<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('my', \App\Http\Controllers\MyController::class)->name('my');
Route::get('project/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
Route::get('project/{project}/items/{item}', [\App\Http\Controllers\ItemController::class, 'show'])->name('item.show');
Route::post('project/{project}/items/{item}/vote', [\App\Http\Controllers\ItemController::class, 'vote'])->name('item.vote');

