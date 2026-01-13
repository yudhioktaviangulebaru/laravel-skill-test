<?php

use App\Http\Controllers\PostController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index'])
    ->name('posts.index');

Route::middleware('auth')->group(function () {
    Route::get('posts/create', [PostController::class, 'create'])
        ->name('posts.create')
        ->can('create', Post::class);

    Route::post('posts', [PostController::class, 'store'])
        ->name('posts.store')
        ->can('create', Post::class);

    Route::get('posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->can('update', 'post');

    Route::put('posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->can('update', 'post');

    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->can('delete', 'post');
});
Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show');
