<?php


use App\Http\Resources\AuthApiController;
use App\Http\Resources\SeriesController;
use App\Models\Episodes;
use App\Models\Series;
use App\Models\User;
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

Route::middleware('auth.resource')->group(function () {
    Route::apiResource('series', SeriesController::class);

    Route::get('/series/{series}/seasons', function (Series $series) {
        return response()->json(['seasons' => $series->seasons]);
    });

    Route::get('/series/{series}/episodes', function (Series $series) {
        return response()->json(['episodes' => $series->episodes]);
    });

    Route::patch('/series/{episodes}/watch', function (Episodes $episodes, Request $request) {
        if ($request->watched) {
            $episodes->watched = 1;
        } else {
            $episodes->watched = 0;
        }

        $episodes->save();
        return $episodes;
    });
});

Route::post('/login', [AuthApiController::class, 'login']);

Route::post('/series/cover', function (Request $request) {
    dd(base64_decode($request->cover));
});

Route::get('/user', [AuthApiController::class, 'user']);
