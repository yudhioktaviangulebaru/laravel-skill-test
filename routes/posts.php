<?php

use App\Http\Controllers\Users\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->only(['index', 'show']);
Route::resource('posts', PostController::class)->except(['index', 'show'])->middleware('auth');
