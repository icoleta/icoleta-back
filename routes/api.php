<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PointController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\PersonController;
use App\Http\Controllers\API\SemesterController;
use App\Http\Controllers\Auth\API\LoginController;

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

// Route::post('/send-reset-email',[UserController::class, 'sendEmailToResetPass']);
// Route::post('/reset-pass',[UserController::class, 'resetPass']);
// Route::post('/login',[UserController::class, 'login']);
// Route::post('/registerUser',[UserController::class, 'registerUser']);

Route::get('semester', [SemesterController::class, 'index']);
Route::get('course', [CourseController::class, 'index']);

Route::prefix('/person')->group(function() {
    Route::post('/', [PersonController::class, 'store']);
});

Route::get('point', [PointController::class, 'index']);

Route::prefix('/company')->group(function() {
    Route::post('/', [CompanyController::class, 'store']);

    Route::prefix('/point')->group(function(){
        Route::get('/', [PointController::class, 'showUserPoints']);
        Route::post('/', [PointController::class, 'store']);
        Route::get('/{id}', [PointController::class, 'show']);
        Route::put('/{id}', [PointController::class, 'update']);
        Route::delete('/{id}', [PointController::class, 'destroy']);
    });
});

Route::prefix('/admin')->group(function() {
    Route::prefix('/company')->group(function() {
        Route::get('/', [CompanyController::class, 'index']);
    });
});

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout']);
