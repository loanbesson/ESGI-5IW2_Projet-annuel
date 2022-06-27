<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SocialeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\NewPasswordController;
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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [NewPasswordController::class, 'reset']);
});

// La redirection vers le provider
Route::get('/login/{provider}', [SocialeController::class,'redirectToProvider']);

// Le callback du provider
Route::get('/login/{provider}/callback', [SocialeController::class,'handleProviderCallback']);

Route::middleware('auth:api', 'verified')->group(function () {
    Route::resource('contacts', ContactController::class, ['except' => ['create', 'edit']]);
    Route::resource('favorites', FavoriteController::class, ['except' => ['create', 'edit']]);
    Route::resource('searches', SearchController::class, ['except' => ['create', 'edit']]);
    Route::resource('properties', PropertyController::class, ['except' => ['create', 'edit']]);
    Route::resource('users', UserController::class)->middleware('can:isAdmin');
    Route::get('/users/{user}/contacts', [UserController::class, 'getContacts']);
    Route::get('/users/{user}/favorites', [UserController::class, 'getFavorites']);
    Route::get('/users/{user}/properties', [UserController::class, 'getProperties']);
});
