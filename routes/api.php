<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthOtpController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('socialLogin', [AuthController::class, 'socialLogin']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgotPassword', [AuthController::class, 'forgotPassword']);

// Route::get('otp_verification', [AuthController::class, 'otpVerification']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('otp_verification', [AuthController::class, 'otpVerification']);
    Route::post('resetPassword', [AuthController::class, 'resetPassword']);
});


// Route::controller(AuthOtpController::class)->group(function(){
//     Route::get('/otp/login', 'login')->name('otp.login');
//     Route::post('/otp/generate', 'generate')->name('otp.generate');
//     Route::get('/otp/verification/{user_id}', 'verification')->name('otp.verification');
//     Route::post('/otp/login', 'loginWithOtp')->name('otp.getlogin');
// });

// Route::get('/auth/google/redirect', function () {
//     return Socialite::driver('google')->redirect();
// });
 
// Route::get('/auth/facebook/callback', function () {
//     $user = Socialite::driver('facebook')->user();
 
//     // $user->token
// });
