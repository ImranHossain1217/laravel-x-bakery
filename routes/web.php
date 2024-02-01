<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;

// Web API Routes
Route::post('/user-registration', [UserController::class, 'UserRegistration']);
Route::post('/user-login', [UserController::class, 'UserLogin']);
Route::post('/send-otp',[UserController::class, 'SendOTPCode']);
Route::post('/verify-otp',[UserController::class, 'VerifyOTPCode']);
Route::post('/reset-password',[UserController::class, 'ResetPassword'])
->middleware([TokenVerificationMiddleware::class]);

// Page Routes
Route::view('/','pages.home');
Route::view('/dashboard','pages.dashboard.dashboard-page');
Route::view('/userLogin','pages.auth.login-page');
Route::view('/userRegistration','pages.auth.registration-page');
Route::view('/sendOtp','pages.auth.send-otp-page');
Route::view('/verifyOtp','pages.auth.verify-otp-page');
Route::view('/resetPassword','pages.auth.reset-pass-page');
Route::view('/userProfile','pages.dashboard.profile-page');