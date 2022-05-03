<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('project/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
Route::get('project/{project}/items/{item}', [\App\Http\Controllers\ItemController::class, 'show'])->name('item.show');

