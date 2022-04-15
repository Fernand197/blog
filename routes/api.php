<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::resource('/posts', PostController::class)->only( ['index', 'show']);
Route::resource('/users', UserController::class)->only( ['index', 'store']);
Route::controller(UserController::class)->group(function () {
    Route::get('/users/{user}/posts',  'listPosts');
    Route::get('/users/{user}/comments',  'listComments');

});
Route::controller(CommentController::class)->group(function () {
    Route::get('/posts/{post}/comments',  'listComments');
    Route::post('/posts/{post}/comments',  'postComment');
    Route::patch('/posts/{post}/comments/{comment}',  'updateComment');
    Route::delete('/posts/{post}/comments/{comment}',  'deleteComment');

});
Route::controller(PostController::class)->group(function () {
    Route::post('/users/{user}/posts', 'postUsers');
    Route::get('/posts/{post}/tags', 'tagsPost');
    Route::patch('/users/{user}/posts/{post}', 'updatePost');
    Route::delete('/users/{user}/posts/{post}', 'deletePost');
});

Route::resource('/tags', TagController::class)->only(['show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
