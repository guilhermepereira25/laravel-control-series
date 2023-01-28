<?php

use App\Http\Controllers\SeasonsController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\EpisodesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\AuthenticateSeries;
use App\Mail\MailSeriesCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource(
    '/series', SerieController::class)->except(
        ['show']
);

Route::middleware('auth')->group(function() {
    Route::get('/', function() {
        return redirect('/login');
    });

    //seasons
    Route::get('/series/{series}/seasons', [SeasonsController::class, 'index'])->name('seasons.index');

    //episodes
    Route::get('/series/seasons/{seasons}/episodes', [EpisodesController::class, 'index'])->name('episodes.index');
    Route::post('/seasons/{season}/episodes', [EpisodesController::class, 'update'])->name('episodes.update');

    //logout somente logado
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/', function() {
    return redirect('/login');
})->middleware(AuthenticateSeries::class);

//Auth::routes();

// Route::get('/email', function () {
//     return new MailSeriesCreated(
//         'Série teste',
//         5,
//         10,
//         20,
//         'Guilherme'
//     );
// });

// Route::get('sendMail', function() {
//     try {
//         Mail::to("hello@example.com")->send(new MailSeriesCreated(
//             'The walking dead',
//             10,
//             5,
//             10,
//             'Guilherme'
//         ));
//     } catch (Throwable $th) {
//         return $th;
//     }
// });

// Route::get('/email', function() {
//     try {
//         $authBearer = env('MAILTRAP_TOKEN');
//         $mailtrapBaseUrl = env('MAILTRAP_URL');

//         $response = Http::withHeaders([
//             'Api-Token' => $authBearer,
//             'Content-Type'  => 'application/json',
//             'Accept'        => 'application/json',
//         ])->post($mailtrapBaseUrl, [
//             "to" => [
//                 "email" => "guiguiugi@email.com",
//                 "name" => "gui teste"
//             ],
//             "from" => [
//                 "email" => "sales@example.com",
//                 "name" => "Example Sales Team"
//             ],
//             "subject" => "Your Example Order Confirmation",
//             "html" => "<p>Congratulations on your order no. <strong>1234</strong>.</p>",
//             "text" => "Congratulations on your order no. 1234",
//         ])->json();

//         return $response;
//     } catch (Throwable $th) {
//         return $th;
//     }
// });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'create'])->name('register.create');
