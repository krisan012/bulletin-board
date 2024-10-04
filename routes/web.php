<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UpvoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/api/user', function () {
    return response()->json(auth()->user());
});


Route::resource('/articles', ArticleController::class);
Route::post('/articles/{article}/comment', [CommentController::class, 'store']);
Route::post('/articles/{article}/upvote', [UpvoteController::class, 'upvote'])->middleware('auth');
Route::delete('/articles/{article}/upvote', [UpvoteController::class, 'removeUpvote'])->middleware('auth');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
