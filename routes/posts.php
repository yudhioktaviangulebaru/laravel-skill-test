<?php

use App\Http\Controllers\PostController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::middleware('auth')
    ->prefix('/posts')
    ->controller(PostController::class)
    ->group(function () {
        Route::post('/', 'store')->name('posts.store')->can('create', Post::class);
        Route::get('/create', 'create')->name('posts.create')->can('create', Post::class);
        Route::get('/{post}/edit', 'edit')->name('posts.edit')->can('update', 'post');
        Route::match(['put', 'patch'], '/{post}', 'update')->name('posts.update')->can('update', 'post');
        Route::delete('/{post}', 'destroy')->name('posts.destroy')->can('delete', 'post');
    });
