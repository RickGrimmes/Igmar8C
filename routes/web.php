<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view ('login');
})->name('login');

Route::post('/login', [UserController::class, 'login'])->name('login.post');

Route::get('/register', function () {
    return view ('register');
})->name('register');

Route::post('/register', [UserController::class, 'register'])->name('register.post');

Route::get('/styles.css', function () {
    return response()->file(public_path('styles.css'));
});

Route::get('/favicon.ico', function () {
    return response()->file(public_path('favicon.ico'));
});

Route::middleware(['tokenIsValid'])->get('/home', function () {
    return view('home');
})->name('home');
