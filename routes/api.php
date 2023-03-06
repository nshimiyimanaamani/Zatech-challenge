<?php

use App\Http\Controllers\Albumcontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/album', Albumcontroller::class);
    Route::apiResource('/song', SongController::class);

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
});
Route::get('/songs', [SongController::class, 'listSongs']);
Route::get('/albums',[Albumcontroller::class,'listAlbums']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

