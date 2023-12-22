<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PointController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\DiscardController;
use App\Http\Controllers\API\PersonController;
use App\Http\Controllers\API\ResiduumController;
use App\Http\Controllers\API\SemesterController;
use App\Http\Controllers\API\RankingController;
use App\Http\Controllers\Auth\API\LoginController;
use App\Http\Controllers\FeedbackController;

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

// Public Routes
Route::get('points', [PointController::class, 'index']);
Route::get('point/{id}', [PointController::class, 'show']);

Route::post('/person', [PersonController::class, 'store']);
Route::post('/company', [CompanyController::class, 'store']);

Route::get('ranking', [RankingController::class, 'ranking']);
Route::get('semester', [SemesterController::class, 'index']);
Route::get('course', [CourseController::class, 'index']);
Route::get('/residuum', [ResiduumController::class, 'index']);
Route::post('feedback', [FeedbackController::class, 'store']);

Route::post('login', [LoginController::class, 'login']);
Route::post('discards', [DiscardController::class, 'createDiscardAsUser']);

// Private Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('/person/discards', [DiscardController::class, 'listUserDiscards']);

    Route::middleware('ensureCompany')->prefix('/company')->group(function () {
        Route::prefix('/point')->group(function () {
            Route::post('/', [PointController::class, 'store']);
            Route::get('/{id}', [PointController::class, 'show']);
            Route::put('/{id}', [PointController::class, 'update']);
            Route::delete('/{id}', [PointController::class, 'destroy']);
            Route::get('/', [PointController::class, 'showCompanyPoints']);
        });
    });

    Route::middleware('ensureAdmin')->prefix('/admin')->group(function () {
        Route::prefix('/person')->group(function () {
            Route::get('/', [PersonController::class, 'index']);
            Route::patch('/{id}', [PersonController::class, 'makeVolunteer']);
        });

        Route::prefix('discards')->group(function () {
            Route::get('/', [DiscardController::class, 'index']);
            Route::post('/', [DiscardController::class, 'store']);
            Route::get('/{id}', [DiscardController::class, 'show']);
            Route::delete('/{id}', [DiscardController::class, 'delete']);
        });

        Route::prefix('/company')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::get('/{id}', [CompanyController::class, 'show']);
            Route::patch('/{id}', [CompanyController::class, 'verify']);
        });

        Route::prefix('/residuum')->group(function () {
            Route::post('/', [ResiduumController::class, 'store']);
            Route::put('/{id}', [ResiduumController::class, 'edit']);
            Route::delete('/{id}', [ResiduumController::class, 'delete']);
        });
    });
});
