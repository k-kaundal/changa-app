<?php

use App\Http\Controllers\web\Auth\LoginController;
use App\Http\Controllers\web\Auth\RegistrationController;
use App\Http\Controllers\web\DashboardController;
use App\Http\Controllers\web\ProfileController;
use App\Http\Controllers\web\UsersController;
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
    return view('welcome');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth'); 

/**
 * Dashboard Controller for all dashboard route 
 */
Route::controller(DashboardController::class)->group(function(){
    Route::get('dashboard','index')->name('dashboard');
    Route::get('users','users')->name('users');
    Route::get('users/edit','edit_user')->name('edit_user');
    Route::get('users/view','view_user')->name('view_user');

   

});

/**
 * User Controller for login registration and forgot password or reset password
 */

 Route::controller(UsersController::class)->group(function(){
    Route::get('login','login')->name('login');
    Route::post('check_login','check_login')->name('check_login');
    Route::get('registration','registration')->name('registration');

});

// Route::get('registration', [RegistrationController::class, 'index'])->name('registration');
// Route::get('login', [LoginController::class, 'index'])->name('login');
Route::get('profile', [ProfileController::class, 'index'])->name('profile');


