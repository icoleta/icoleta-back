<?php

use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\API\LoginController;
use App\Http\Controllers\Auth\API\RegisterController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/send-reset-email',[UserController::class, 'sendEmailToResetPass']);
Route::post('/reset-pass',[UserController::class, 'resetPass']);
Route::post('/login',[UserController::class, 'login']);
Route::post('/registerUser',[UserController::class, 'registerUser']);

Route::post('/company/register', [CompanyController::class, 'registerCompany'], 'registerCompany');

Route::prefix('auth')->group(function (){
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('register', [RegisterController::class, 'register']);
});
