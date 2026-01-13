<?php

use App\Http\Controllers\PostController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index'])
    ->name('posts.index');

Route::post('posts', [PostController::class, 'store'])
    ->name('posts.store')
    ->middleware('auth')
    ->can('create', Post::class);

Route::get('posts/create', [PostController::class, 'create'])
    ->name('posts.create')
    ->middleware('auth')
    ->can('create', Post::class);

Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show');

Route::get('posts/{post}/edit', [PostController::class, 'edit'])
    ->name('posts.edit')
    ->middleware('auth')
    ->can('update', 'post');

Route::match(['put', 'patch'], 'posts/{post}', [PostController::class, 'update'])
    ->name('posts.update')
    ->middleware('auth')
    ->can('update', 'post');

Route::delete('posts/{post}', [PostController::class, 'destroy'])
    ->name('posts.destroy')
    ->middleware('auth')
    ->can('delete', 'post');
