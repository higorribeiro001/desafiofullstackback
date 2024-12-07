<?php

use App\Http\Controllers\Api\UserController;
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

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Route::get('/users', [UserController::class, 'index']);
// Route::get('/user/{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
// Route::post('/user', [UserController::class, 'store']);

Route::apiResource('user', 'App\Http\Controllers\Api\UserController');
Route::apiResource('phone', 'App\Http\Controllers\Api\PhoneController');
Route::apiResource('job', 'App\Http\Controllers\Api\MailNewsController');
