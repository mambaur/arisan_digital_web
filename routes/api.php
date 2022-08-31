<?php

use App\Http\Controllers\API\ArisanHistoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\MemberController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    /*
    |--------------------------------------------------------------------------
    | Auth Routes
    |--------------------------------------------------------------------------
    |
    | Manage auth data API
    |
    */

    Route::get('/user', [AuthController::class, 'show']);
    
    Route::get('/logout', [AuthController::class, 'logout']);
    
    /*
    |--------------------------------------------------------------------------
    | Members Routes
    |--------------------------------------------------------------------------
    |
    | Manage members data API
    |
    */

    Route::get('/members', [MemberController::class, 'index']);

    Route::get('/member/{id}', [MemberController::class, 'show']);

    Route::post('/member/store', [MemberController::class, 'store']);

    Route::patch('/member/update/{id}', [MemberController::class, 'update']);

    Route::patch('/member/update/status-paid/{id}', [MemberController::class, 'updateStatusPaid']);

    Route::patch('/member/reset/status-paid/{id}', [MemberController::class, 'resetStatusPaid']);

    Route::delete('/member/delete/{id}', [MemberController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Groups Routes
    |--------------------------------------------------------------------------
    |
    | Manage groups data API
    |
    */

    Route::get('/groups/{id}', [GroupController::class, 'index']);

    Route::get('/group/{id}', [GroupController::class, 'show']);

    Route::post('/group/store', [GroupController::class, 'store']);

    Route::patch('/group/update/{id}', [GroupController::class, 'update']);

    Route::patch('/group/update/status/{id}', [GroupController::class, 'updateStatus']);

    Route::patch('/group/update/notes/{id}', [GroupController::class, 'updateNotes']);

    Route::delete('/group/delete/{id}', [GroupController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Arisan History Routes
    |--------------------------------------------------------------------------
    |
    | Manage arisan history data API
    |
    */

    Route::get('/arisan-histories/{id}', [ArisanHistoryController::class, 'index']);

    Route::get('/arisan-history/{id}', [ArisanHistoryController::class, 'show']);

    Route::post('/arisan-history/store', [ArisanHistoryController::class, 'store']);

    Route::post('/arisan-history/delete/{id}', [ArisanHistoryController::class, 'destroy']);

});