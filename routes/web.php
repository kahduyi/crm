<?php

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

Route::get('/', function () {
    return view('organization.user.login-employee');
});

/**
 *
 */
Route::get('/clear-cache', function () {
//    $exitCode = Artisan::call('optimize');
    $exitCode = Artisan::call('optimize');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('clear-compiled');
    return $exitCode;
    return 'DONE'; //Return anything
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
