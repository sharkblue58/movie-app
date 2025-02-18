<?php


use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SocialEmailController;
use App\Http\Controllers\RolesAndPermissionsController;


Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send-otp', [OtpController::class, 'sendOTP']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOTP']);
    Route::Post('/social-login', [SocialEmailController::class, 'socialLogin']);
    Route::post('/reset-password', [OtpController::class, 'resetPassword']);
});



Route::group(['prefix' => 'v1/auth', 'middleware' => 'auth:api'], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/users/categories', [UserController::class, 'userLikedCategories']);
    Route::post('/users/categories', [UserController::class, 'storeUserLikedCategories']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/forget-password', [UserController::class, 'forgetPassword']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::post('/contact-status', [ContactController::class, 'setContactStatus']);
    Route::post('/contacts', [ContactController::class, 'storeContact']);
    Route::post('/contacts', [ContactController::class, 'allContacts']);
    Route::get('categories', [CategoryController::class, 'getAllCategories']);
    Route::apiResource('movies', MovieController::class);
    Route::resource('role-permission', RolesAndPermissionsController::class)->middleware('can:edit settings');
});
